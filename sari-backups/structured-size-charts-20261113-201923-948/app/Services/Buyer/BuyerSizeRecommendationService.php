<?php

namespace App\Services\Buyer;

/** Validates model-selected variant IDs against server inventory and supplied chart context. */
class BuyerSizeRecommendationService
{
    public function measurements(string $question, array $history = []): array
    {
        $result = [];
        $texts = array_map(fn ($turn) => ($turn['role'] ?? '') === 'user' ? (string) ($turn['content'] ?? '') : '', array_slice($history, -8));
        $texts[] = $question;
        foreach ($texts as $text) {
            foreach (['chest'=>'chest|bust|dibdib','waist'=>'waist|bewang','hips'=>'hips?|balakang','foot'=>'foot(?: length)?|feet length|paa'] as $name => $aliases) {
                if (preg_match_all('/\b(?:'.$aliases.')(?:\s+circumference)?\s*(?:ko|is|ay)?\s*[:=]?\s*(\d+(?:\.\d+)?)\s*(cm|centimeters?|inches?|in|\")/iu', $text, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $value = (float) $match[1];
                        if (mb_strtolower($match[2]) !== 'cm' && !str_starts_with(mb_strtolower($match[2]), 'centimeter')) $value *= 2.54;
                        if ($value > 0 && $value <= 250) $result[$name.'_cm'] = round($value, 2);
                    }
                }
            }
        }
        return $result;
    }

    public function apply(array $products, array $recommendations, array $attachedChartIds, array $measurements): array
    {
        foreach ($products as &$product) {
            $role = (string) ($product['outfit_role'] ?? '');
            $required = match ($role) { 'Bottom'=>'waist_cm,hips_cm', 'Footwear'=>'foot_cm', default=>'chest_cm' };
            $required = explode(',', $required);
            $product['needed_measurements'] = array_values(array_filter($required, fn ($key) => !isset($measurements[$key])));
            if (($product['size_recommendation_basis'] ?? '') === 'requested_label') continue;
            foreach ($recommendations as $recommendation) {
                if ((int) ($recommendation['product_id'] ?? 0) !== (int) $product['id']) continue;
                if (!in_array((int) $product['id'], $attachedChartIds, true) || $product['needed_measurements']) continue;
                if ((int) ($recommendation['size_chart_product_id'] ?? 0) !== (int) $product['id']) continue;
                $used = (array) ($recommendation['measurements_used'] ?? []);
                if (array_diff($required, $used) || array_diff($used, array_keys($measurements))) continue;
                $variant = collect($product['variants'] ?? [])->first(fn ($v) =>
                    (int) ($v['id'] ?? 0) > 0 && (int) $v['id'] === (int) ($recommendation['variant_id'] ?? 0)
                    && (int) ($v['stock'] ?? 0) > 0 && filled($v['size'] ?? null));
                if (!$variant) continue;
                // This is a chart-based styling suggestion, not a guarantee of fit.
                $product['suggested_variant'] = $variant;
                $product['size_recommendation_basis'] = 'seller_chart_suggestion';
                $product['size_fit_confirmed'] = false;
                $product['price'] = (float) $variant['price'];
                break;
            }
        }
        unset($product);
        return $products;
    }

    public function summary(array $products): string
    {
        $lines = [];
        foreach ($products as $product) {
            $variant = $product['suggested_variant'] ?? null;
            $basis = $product['size_recommendation_basis'] ?? '';
            $line = $product['name'].': ';
            if ($variant && in_array($basis, ['seller_chart_suggestion', 'requested_label'], true)) {
                $line .= ($basis === 'requested_label' ? 'Requested size ' : 'Suggested size ').($variant['size'] ?? 'not listed');
                if (!empty($variant['color'])) $line .= ' · Color '.$variant['color'];
                $line .= ' · ₱'.number_format((float) $variant['price'], 2).'.';
                $line .= $basis === 'seller_chart_suggestion' ? ' Based on the attached seller chart and your measurements; fit is not guaranteed.' : ' Available seller label; body fit has not been checked.';
            } else {
                if (!empty($variant['color'])) $line .= 'Color suggestion: '.$variant['color'].'. ';
                $needed = $product['needed_measurements'] ?? [];
                $line .= $needed ? 'To suggest one size, share '.implode(' and ', array_map(fn ($value) => str_replace('_cm', ' (cm)', $value), $needed)).'.' : 'A readable seller chart is needed before choosing one size. Please check the chart or ask the seller.';
            }
            $lines[] = $line;
        }
        return implode("\n", $lines);
    }
}
