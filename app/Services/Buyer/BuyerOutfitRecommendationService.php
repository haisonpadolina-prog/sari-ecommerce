<?php

namespace App\Services\Buyer;

use App\Services\Products\ProductSizeChartService;

/** Deterministic clothing suggestions from the buyer-visible catalog, never model-invented inventory. */
class BuyerOutfitRecommendationService
{
    public function normalizeClothingTerms(string $text): string
    {
        return preg_replace('/\b(?:t[\s\x{2010}-\x{2015}-]*shirts?|thirt|tshrit|tee(?:[ -]?shirts?)?)\b/iu', 't-shirt', $text);
    }

    public function supports(string $question): bool
    {
        return (bool) preg_match('/\b(outfit|ootd|clothing|clothes|damit|shirt|polo|jeans|shorts|pants|trousers|blouse|dress|skirt|jacket|hoodie|sneakers|sapatos)\b/iu', $this->normalizeClothingTerms($question));
    }

    public function recommend(array $catalog, string $question, array $history = [], array $charts = [], array $measurements = []): array
    {
        $text = mb_strtolower($this->normalizeClothingTerms($question));
        // A follow-up inherits only the most recent user's clothing request, not assistant claims.
        if (! $this->supports($question)) {
            foreach (array_reverse($history) as $turn) {
                if (($turn['role'] ?? '') === 'user' && $this->supports((string) ($turn['content'] ?? ''))) {
                    $text = mb_strtolower($this->normalizeClothingTerms((string) $turn['content'])).'; '.$text;
                    break;
                }
            }
        }
        $full = (bool) preg_match('/\b(full|complete|buong|kumpletong)\s+(outfit|look|ootd)\b/u', $text);
        $wanted = $this->kind($text);
        $type = null;
        if (! $full && preg_match('/\b(jeans|shorts|trousers|pants|skirt|polo|blouse|hoodie|jacket|dress|t-shirt|shirt|sneakers|sandals)\b/u', $text, $typeMatch)) {
            $type = $typeMatch[1];
        }
        $size = null;
        if (preg_match_all('/\b(?:size|sukat)(?:\s*(?:ko|is|ay))?\s*[:=]?\s*(extra\s+large|extra\s+small|small|medium|large|xxl|2xl|3xl|xl|xs|s|m|l|\d{2})\b/u', $text, $m)) {
            $size = $this->size(end($m[1]));
        }
        $colorText = preg_replace('/\b(?:skin tone|skin|kutis|balat)\s*[:=]?\s*[^;,.]+|\b(?:brown|dark|fair|light|medium|tan)\s+skin\b/u', '', $text);
        $color = null;
        if (preg_match_all('/\b(black|white|blue|red|green|pink|yellow|brown|grey|gray|purple|orange|beige|navy|violet|cream|ivory|olive|charcoal)\b/u', $colorText, $m)) {
            $color = $this->color(end($m[1]));
        }
        $tagalog = (bool) preg_match('/\b(ako|ko|gusto|paki|damit|bagay|kulay|anong|sukat|sapatos|buong)\b/u', mb_strtolower($question));
        $budget = null;
        if (preg_match('/(?:under|below|up to|max(?:imum)?|budget|hanggang|badyet)\s*(?:of|is|:|₱|php|pesos)?\s*(?:₱|php)?\s*([0-9][0-9,]*(?:\.[0-9]{1,2})?)/iu', $text, $budgetMatch)) {
            $budget = (float) str_replace(',', '', $budgetMatch[1]);
        }
        $pool = [];
        foreach ($catalog as $product) {
            $role = $this->kind(mb_strtolower($this->normalizeClothingTerms((string) ($product['name'] ?? ''))));
            if (! $role || (! $full && $wanted && $role !== $wanted)) {
                continue;
            }
            if ($type && ! preg_match('/\b'.preg_quote($type, '/').'s?\b/u', mb_strtolower($this->normalizeClothingTerms((string) ($product['name'] ?? ''))))) {
                continue;
            }
            $variants = [];
            foreach ((array) ($product['variants'] ?? []) as $variant) {
                if ((int) ($variant['stock'] ?? 0) <= 0) {
                    continue;
                }
                $options = array_change_key_case((array) ($variant['options'] ?? []));
                $vColor = (string) ($options['color'] ?? $options['colour'] ?? $variant['color'] ?? '');
                $vSize = (string) ($options['size'] ?? $options['shoe size'] ?? $variant['size'] ?? '');
                // Size is a product-label preference, not proof of body fit.
                if ($size !== null && (! $full || $role !== 'Footwear') && $this->size($vSize) !== $size) {
                    continue;
                }
                if ($color !== null && $this->color($vColor) !== $color) {
                    continue;
                }
                $variants[] = array_merge($variant, ['color' => $vColor ?: null, 'size' => $vSize ?: null]);
            }
            if (! empty($product['has_variants']) || ! empty($product['variants'])) {
                if (! $variants) {
                    continue;
                }
            } elseif ((int) ($product['stock'] ?? 0) <= 0 || $size !== null || $color !== null) {
                continue;
            }
            // Prefer neutral combinations when the buyer has not requested a color.
            if ($color === null) {
                usort($variants, fn ($a, $b) => ((int) ! in_array($this->color((string) $a['color']), ['black', 'white', 'navy', 'beige', 'gray', 'charcoal'], true))
                    <=> ((int) ! in_array($this->color((string) $b['color']), ['black', 'white', 'navy', 'beige', 'gray', 'charcoal'], true)));
            }
            if ($budget !== null) {
                $variants = array_values(array_filter($variants, fn ($variant) => (float) $variant['price'] <= $budget));
            }
            if ((! empty($product['has_variants']) || ! empty($product['variants'])) && ! $variants) {
                continue;
            }
            $pick = $variants[0] ?? null;
            if ($budget !== null && (float) ($pick['price'] ?? $product['price'] ?? 0) > $budget) {
                continue;
            }
            $id = (int) $product['id'];
            $image = (string) ($product['image'] ?? '');
            if (str_starts_with($image, 'buyer/')) {
                $image = '/'.$image;
            }
            $candidate = [
                'id' => $id, 'name' => (string) $product['name'], 'category' => (string) ($product['category'] ?? 'Fashion'),
                'seller' => (string) ($product['shop_name'] ?? 'SARI Seller'),
                'price' => (float) ($pick['price'] ?? $product['price'] ?? 0), 'stock' => (int) ($product['stock'] ?? 0),
                'image' => $image, 'url' => route('buyer.product.details', ['product' => $id]),
                'variants' => $variants, 'suggested_variant' => $pick,
                'outfit_role' => $role, 'size_fit_confirmed' => false,
                'size_recommendation_basis' => $size !== null && (! $full || $role !== 'Footwear') ? 'requested_label' : 'needs_measurements',
                'size_guide_available' => ! empty($product['size_guide_image']),
                'size_guide_image_url' => $product['size_guide_image'] ?? null,
                'recommendation_basis' => 'available_variant',
            ];
            $candidate['_size_rank'] = isset($charts[$id]) ? 1 : 0;
            if (isset($charts[$id]) && $candidate['size_recommendation_basis'] !== 'requested_label') {
                $match = app(ProductSizeChartService::class)->match($charts[$id], $measurements, $variants, $measurements['_fit'] ?? null);
                if ($match['status'] === 'matched') {
                    $candidate['_size_rank'] = 3;
                    $candidate['price'] = (float) $match['variant']['price'];
                    if ($budget !== null && $candidate['price'] > $budget) {
                        continue;
                    }
                } elseif (in_array($match['status'], ['no_match', 'size_unavailable', 'fit_mismatch'], true)) {
                    $candidate['_size_rank'] = -1;
                }
            }
            $pool[] = $candidate;
        }
        $products = [];
        $missing = [];
        usort($pool, fn ($a, $b) => ($b['_size_rank'] <=> $a['_size_rank']) ?: ($budget !== null ? $a['price'] <=> $b['price'] : 0));
        if ($full) {
            $roles = ['Top', 'Bottom', 'Footwear'];
            if (! collect($pool)->contains(fn ($p) => $p['outfit_role'] === 'Top') && collect($pool)->contains(fn ($p) => $p['outfit_role'] === 'Dress')) {
                $roles = ['Dress', 'Footwear'];
            }
            foreach ($roles as $role) {
                $remaining = $budget === null ? INF : $budget - array_sum(array_column($products, 'price'));
                $item = collect($pool)->first(fn ($p) => $p['outfit_role'] === $role && $p['price'] <= $remaining);
                if ($item) {
                    $products[] = $item;
                } else {
                    $missing[] = $role;
                }
            }
            $accessory = collect($pool)->first(fn ($p) => $p['outfit_role'] === 'Accessory');
            if ($accessory && ($budget === null || array_sum(array_column($products, 'price')) + $accessory['price'] <= $budget)) {
                $products[] = $accessory;
            }
        } else {
            $products = array_slice($pool, 0, 4);
        }
        $lines = [$full ? ($tagalog ? 'Mga available na piraso para sa outfit mo:' : 'Available pieces for your outfit:') : ($tagalog ? 'Ito ang matching products at available variants:' : 'Matching products and available variants:')];
        foreach ($products as $product) {
            $variant = $product['suggested_variant'];
            $details = [];
            if ($variant) {
                if ($variant['color']) {
                    $details[] = 'Color: '.$variant['color'];
                }
                if ($variant['size'] && ($product['size_recommendation_basis'] ?? '') === 'requested_label') {
                    $details[] = 'Size label: '.$variant['size'];
                }
                if (! $details) {
                    $details[] = (string) ($variant['label'] ?? 'Available variant');
                }
            } else {
                $details[] = $tagalog ? 'Walang size/color variant na nilista ang seller' : 'Seller did not list size/color variants';
            }
            $lines[] = $product['outfit_role'].': '.$product['name'].' — '.implode(' · ', $details).' — ₱'.number_format($product['price'], 2);
        }
        if (! $products) {
            $lines[] = $tagalog ? 'Walang matching in-stock na damit sa catalog para sa request mo.' : 'No matching in-stock clothing was found for these preferences.';
        }
        if ($missing) {
            $lines[] = ($tagalog ? 'Kulang sa matching catalog items: ' : 'Missing matching catalog pieces: ').implode(', ', $missing).'.';
        }
        if ($products) {
            $lines[] = $tagalog
                ? 'Ang size label ay available option, hindi pa kumpirmadong fit. Para sa eksaktong size, ikumpara ang chest/waist/hip o foot measurements sa seller size chart. Hindi sapat ang height at weight lang.'
                : 'Size labels are available options, not confirmed fit. Compare relevant chest/waist/hip or foot measurements with each seller chart; height and weight alone cannot determine size.';
        }
        if ($budget !== null) {
            $lines[] = 'Budget cap: ₱'.number_format($budget, 2).($full ? ' for the outfit pieces together' : ' per item').'; shipping is not included.';
        }
        if ($color !== null && $full) {
            $lines[] = $tagalog ? 'Inapply ang hininging kulay sa bawat piraso; puwedeng alisin ang color preference para sa mas maraming kombinasyon.' : 'The requested color was applied to each piece; remove that preference for more combinations.';
        }

        return ['products' => $products, 'reply' => implode("\n", $lines), 'missing_roles' => $missing, 'full_outfit' => $full, 'budget_cap' => $budget];
    }

    public function roleForProduct(string $name): ?string
    {
        return $this->kind(mb_strtolower($name));
    }

    private function kind(string $text): ?string
    {
        return match (true) {
            (bool) preg_match('/\b(shoes?|sneakers?|sandals?|footwear|sapatos|boots?)\b/u', $text) => 'Footwear',
            (bool) preg_match('/\b(dress|dresses|gown)\b/u', $text) => 'Dress',
            (bool) preg_match('/\b(jeans|shorts|pants|trousers|chino|skirt)\b/u', $text) => 'Bottom',
            (bool) preg_match('/\b(shirt|shirts|t-shirt|t-shirts|polo|blouse|top|hoodie|jacket|sweater)\b/u', $text) => 'Top',
            (bool) preg_match('/\b(bag|watch|belt|necklace|accessories|accessory)\b/u', $text) => 'Accessory',
            default => null,
        };
    }

    private function size(string $size): string
    {
        $value = mb_strtolower(trim($size));

        return ['extra small' => 'xs', 'small' => 's', 'medium' => 'm', 'large' => 'l', 'extra large' => 'xl', '2xl' => 'xxl'][$value] ?? $value;
    }

    private function color(string $color): string
    {
        return str_replace('grey', 'gray', mb_strtolower(trim($color)));
    }
}
