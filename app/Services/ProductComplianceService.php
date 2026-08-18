<?php

namespace App\Services;

class ProductComplianceService
{
    public function scan(string $name, ?string $description, string $category): array
    {
        $text = mb_strtolower(trim($name . ' ' . ($description ?? '') . ' ' . $category));

        foreach (config('sari_compliance.exceptions', []) as $exception) {
            $text = str_replace(mb_strtolower($exception), ' ', $text);
        }

        $matched = [];
        $reasons = [];
        $highestRisk = 'low';

        $weight = [
            'low' => 0,
            'medium' => 1,
            'high' => 2,
        ];

        foreach (config('sari_compliance.rules', []) as $rule) {
            $ruleMatched = [];

            foreach ($rule['terms'] as $term) {
                if ($this->containsTerm($text, mb_strtolower($term))) {
                    $ruleMatched[] = $term;
                    $matched[] = $term;
                }
            }

            if ($ruleMatched !== []) {
                $reasons[] = $rule['label'];

                if ($weight[$rule['risk']] > $weight[$highestRisk]) {
                    $highestRisk = $rule['risk'];
                }
            }
        }

        $matched = array_values(array_unique($matched));
        $reasons = array_values(array_unique($reasons));

        return [
            'flagged' => $matched !== [],
            'risk' => $highestRisk,
            'matched_terms' => $matched,
            'reason' => $reasons !== []
                ? implode('; ', $reasons)
                : 'No configured prohibited or restricted keyword matched.',
        ];
    }

    private function containsTerm(string $text, string $term): bool
    {
        // Phrase / punctuation-safe boundary matching.
        $pattern = '/(?<![\pL\pN])' . preg_quote($term, '/') . '(?![\pL\pN])/iu';

        return preg_match($pattern, $text) === 1;
    }
}
