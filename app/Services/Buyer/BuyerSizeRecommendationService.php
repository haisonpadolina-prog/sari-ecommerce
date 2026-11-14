<?php

namespace App\Services\Buyer;

use App\Models\Catalog\SellerProductSizeChart;
use App\Services\Products\ProductSizeChartService;
use Illuminate\Support\Facades\Schema;

/** Product-specific range matching. Language models cannot select or invent a fitted size. */
class BuyerSizeRecommendationService
{
    private array $chartCache = [];

    private ?bool $chartTableReady = null;

    public function charts(array $productIds): array
    {
        $this->chartTableReady ??= Schema::hasTable('seller_product_size_charts');
        if (! $this->chartTableReady) {
            return [];
        }
        $missing = array_values(array_filter(array_unique(array_map('intval', $productIds)), fn ($id) => $id > 0 && ! array_key_exists($id, $this->chartCache)));
        if ($missing) {
            foreach ($missing as $id) {
                $this->chartCache[$id] = null;
            }
            foreach (SellerProductSizeChart::query()->whereIn('seller_product_id', $missing)->get() as $chart) {
                $this->chartCache[$chart->seller_product_id] = $chart->toArray();
            }
        }

        return array_filter(array_intersect_key($this->chartCache, array_flip($productIds)));
    }

    public function measurements(string $question, array $history = []): array
    {
        $result = [];
        $texts = array_map(fn ($turn) => ($turn['role'] ?? '') === 'user' ? (string) ($turn['content'] ?? '') : '', array_slice($history, -8));
        $texts[] = $question;
        foreach ($texts as $text) {
            foreach (['chest' => 'chest|bust|dibdib', 'waist' => 'waist|bewang', 'hips' => 'hips?|balakang', 'foot' => 'foot(?: length)?|feet length|paa'] as $name => $aliases) {
                if (preg_match_all('/\b(?:'.$aliases.')(?:\s+circumference)?\s*(?:ko|is|ay)?\s*[:=]?\s*(\d+(?:\.\d+)?)\s*(cm|centimeters?|inches?|in|\")/iu', $text, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $value = (float) $match[1];
                        if (mb_strtolower($match[2]) !== 'cm' && ! str_starts_with(mb_strtolower($match[2]), 'centimeter')) {
                            $value *= 2.54;
                        }
                        if ($value > 0 && $value <= 250) {
                            $result[$name.'_cm'] = round($value, 2);
                        }
                    }
                }
            }
            if (preg_match('/\b(?:fit preference|preferred fit|fit)\s*[:=]?\s*(regular|slim|relaxed)\b/iu', $text, $fit)) {
                $result['_fit'] = mb_strtolower($fit[1]);
            }
        }
        $combined = implode('; ', $texts);
        $confirmed = (bool) preg_match('/measurement basis:\s*body circumference confirmed/iu', $combined);
        $issues = [];
        if (! $confirmed) {
            foreach (['chest_cm' => 50, 'waist_cm' => 40, 'hips_cm' => 45] as $key => $threshold) {
                if (isset($result[$key]) && $result[$key] < $threshold) {
                    $issues[] = 'Please confirm '.str_replace('_cm', '', $key).' '.$result[$key].' cm: full body circumference, inches, or laid-flat garment width? For children, confirm the full body circumference.';
                }
            }
        }
        if (preg_match('/(?:chest|waist|hips?).{0,24}(?:flat|width)|(?:flat|width).{0,24}(?:chest|waist|hips?)/iu', $question)) {
            $issues[] = 'Use full body circumference. A laid-flat garment width is not the same measurement.';
        }
        if ($issues) {
            $result['_issues'] = $issues;
        }

        return $result;
    }

    /** Legacy model arguments are deliberately ignored; only saved seller ranges are authoritative. */
    public function apply(array $products, array $recommendations, array $attachedChartIds, array $measurements): array
    {
        $charts = $this->charts(array_column($products, 'id'));
        $ready = $this->chartTableReady;
        $matcher = app(ProductSizeChartService::class);
        foreach ($products as &$product) {
            $product['size_fit_confirmed'] = false;
            if (($product['outfit_role'] ?? '') === 'Accessory') {
                $product['needed_measurements'] = [];
                $product['size_recommendation_basis'] = 'not_applicable';
                $product['size_match_status'] = 'not_applicable';
                $product['size_match_note'] = 'Check the seller’s accessory dimensions; clothing size matching does not apply.';

                continue;
            }
            $chart = $charts[(int) $product['id']] ?? null;
            $type = ($chart['garment_type'] ?? null) ?? match ($product['outfit_role'] ?? '') {
                'Bottom' => 'bottom','Footwear' => 'footwear','Dress' => 'dress',default => 'top'
            };
            $required = ProductSizeChartService::dimensions($type);
            $product['needed_measurements'] = array_values(array_map(fn ($key) => $key.'_cm', array_filter($required, fn ($key) => ! isset($measurements[$key.'_cm']))));
            if (($product['size_recommendation_basis'] ?? '') === 'requested_label' && empty($measurements['_issues']) && (! $chart || $product['needed_measurements'])) {
                $product['size_match_status'] = 'requested_label';

                continue;
            }
            // No inherited first-variant size may masquerade as a fit recommendation.
            $product['size_recommendation_basis'] = 'needs_measurements';
            $matched = ! empty($measurements['_issues']) ? ['status' => 'clarification_needed', 'issues' => $measurements['_issues']]
                : (! $ready ? ['status' => 'chart_data_unavailable'] : (! $chart ? ['status' => 'chart_missing']
                : $matcher->match($chart, $measurements, (array) ($product['variants'] ?? []), $measurements['_fit'] ?? null)));
            $product['size_match_status'] = $matched['status'];
            if (isset($matched['missing'])) {
                $product['needed_measurements'] = $matched['missing'];
            }
            if ($matched['status'] === 'matched') {
                $product['suggested_variant'] = $matched['variant'];
                $product['size_recommendation_basis'] = 'structured_chart';
                $product['size_match_evidence'] = $matched['evidence'];
                $product['size_chart_fit'] = $matched['fit'];
                $product['price'] = (float) $matched['variant']['price'];
            }
            $product['size_match_note'] = $this->note($matched, $product['needed_measurements']);
        }
        unset($product);

        return $products;
    }

    private function note(array $matched, array $missing): string
    {
        return match ($matched['status']) {
            'matched' => implode('; ', $matched['evidence']).'. Seller-declared '.$matched['fit'].' fit; verify before buying.',
            'clarification_needed' => implode(' ', $matched['issues'] ?? []),
            'chart_data_unavailable' => 'Structured size data is not available yet. The server migration needs to be installed.',
            'chart_missing' => 'This seller has not entered structured measurements for this product. A chart image alone is not used for automatic size selection.',
            'measurements_needed' => 'Share '.implode(' and ', array_map(fn ($key) => str_replace('_cm', ' (cm)', $key), $missing)).' as full body measurements.',
            'fit_mismatch' => 'Your preferred fit differs from the seller chart’s intended fit. Ask the seller for a chart for that fit.',
            'ambiguous' => 'Your measurements match more than one seller size ('.implode(', ', $matched['sizes']).'). Ask the seller to clarify before choosing.',
            'size_unavailable' => 'The matching seller size '.$matched['size'].' is not available in the current in-stock variants or requested color.',
            default => 'Your measurements do not match a size in this seller chart. I will not select a different size without a valid match.',
        };
    }

    public function summary(array $products, bool $tagalog = false): string
    {
        $lines = [];
        foreach ($products as $product) {
            $variant = $product['suggested_variant'] ?? null;
            $basis = $product['size_recommendation_basis'] ?? '';
            $line = $product['name'].': ';
            if ($basis === 'not_applicable') {
                $line .= (! empty($variant['color']) ? 'Color '.$variant['color'].'. ' : '').($product['size_match_note'] ?? 'Check seller accessory dimensions.');
            } elseif ($variant && in_array($basis, ['structured_chart', 'requested_label', 'estimated_image'], true)) {
                $line .= ($basis === 'requested_label' ? 'Requested size ' : ($basis === 'estimated_image' ? 'Estimated size ' : 'Suggested size ')).($variant['size'] ?? 'not listed');
                if (! empty($variant['color'])) {
                    $line .= ' · Color '.$variant['color'];
                }
                $line .= ' · ₱'.number_format((float) $variant['price'], 2).'. ';
                $line .= $basis === 'requested_label' ? 'Available seller label; body fit has not been checked.' : ($product['size_match_note'] ?? 'Seller chart match; fit is not guaranteed.');
            } else {
                $line .= $product['size_match_note'] ?? 'Seller measurements are needed before suggesting a size.';
            }
            $lines[] = $line;
        }
        $summary = implode("\n", $lines);

        return ! $tagalog ? $summary : strtr($summary, [
            'Estimated size ' => 'Tinatayang size ', 'Requested size ' => 'Hiningi mong size ', 'Suggested size ' => 'Iminungkahing size ', ' · Color ' => ' · Kulay ',
            'Available seller label; body fit has not been checked.' => 'Available ang seller size label na ito; hindi pa na-check ang body fit.',
            'Seller-declared ' => 'Ayon sa seller: ', ' fit; verify before buying.' => ' fit; i-check bago bumili.',
            ' is within ' => ' ay nasa range na ',
            'Share ' => 'Ibigay ang ', ' as full body measurements.' => ' bilang buong sukat ng katawan.',
            'This seller has not entered structured measurements for this product. A chart image alone is not used for automatic size selection.' => 'Wala pang structured measurements ang seller para sa produktong ito. Hindi sapat ang chart image lang para sa automatic size matching.',
            'Your preferred fit differs from the seller chart’s intended fit. Ask the seller for a chart for that fit.' => 'Iba ang preferred fit mo sa fit ng seller chart. Humingi ng chart para sa gusto mong fit.',
            'Your measurements do not match a size in this seller chart. I will not select a different size without a valid match.' => 'Walang katugmang size sa seller chart para sa measurements mo. Hindi ako pipili nang walang valid match.',
            'Please confirm ' => 'Pakilinaw ang ',
            ': full body circumference, inches, or laid-flat garment width? For children, confirm the full body circumference.' => ': paikot ba sa katawan, inches, o lapad ng nakalatag na damit? Para sa bata, kumpirmahin ang buong circumference.',
        ]);
    }
}
