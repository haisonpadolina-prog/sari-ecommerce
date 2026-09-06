<?php

namespace App\Services;

use App\Models\ProductModerationLog;
use App\Models\SellerProduct;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProductComplianceService
{
    public function __construct(
        private AiProductInspector $aiInspector
    ) {
    }

    /**
     * Multi-layer product screening:
     * 1) local policy rules (fast fallback / hard signals)
     * 2) Gemini marketplace policy inspector (text + optional image)
     * 3) combined risk decision
     *
     * IMPORTANT: this service never approves products, issues warnings,
     * or suspends sellers. Those remain administrator decisions.
     */
    public function scan(
        string $name,
        ?string $description,
        string $category,
        UploadedFile|string|null $image = null
    ): array {
        $text = trim(implode("\n", array_filter([
            "Product name: {$name}",
            "Category: {$category}",
            $description ? "Description: {$description}" : null,
        ])));

        $local = $this->scanLocalRules($text);
        $ai = $this->aiInspector->inspect($name, $description, $category, $image);

        $medium = (int) config('sari_compliance.thresholds.medium', 35);
        $high = (int) config('sari_compliance.thresholds.high', 70);

        if ($ai['available']) {
            /*
             * Context-aware behavior:
             * - hard local rules always flag
             * - otherwise completed AI classification controls ambiguous terms
             * - local evidence still contributes to the displayed score
             */
            $flagged = (bool) $local['hard_flag'] || (bool) $ai['flagged'];

            if ($local['hard_flag']) {
                $score = max((int) $local['score'], (int) $ai['score']);
            } elseif ($ai['risk'] === 'low') {
                // Let AI context clear ambiguous keyword-only matches.
                $score = max((int) $ai['score'], min(29, (int) $local['score']));
            } else {
                $score = max((int) $local['score'], (int) $ai['score']);
            }
        } else {
            // Conservative offline fallback: local medium/high match goes to admin review.
            $score = max((int) $local['score'], (int) ($ai['score'] ?? 0));
            $flagged = $score >= $medium || (bool) ($ai['flagged'] ?? false);
        }

        if ($flagged && $score < $medium) {
            $score = $medium;
        }

        $score = min(100, max(0, $score));
        $risk = $score >= $high
            ? 'high'
            : ($score >= $medium ? 'medium' : 'low');

        // Respect an explicit AI high risk even if score normalization changed.
        if ($ai['available'] && $ai['risk'] === 'high') {
            $risk = 'high';
            $score = max($score, $high);
            $flagged = true;
        }

        $matchedRules = array_values(array_unique(array_filter([
            ...$local['matched_rules'],
            $ai['policy_category'] !== 'none'
                ? $this->humanizePolicyCategory($ai['policy_category'])
                : null,
        ])));

        $engine = ['local-rules'];
        $engine[] = $ai['available'] ? 'gemini-ai-inspector' : 'gemini-' . $ai['status'];

        return [
            'flagged' => $flagged,
            'risk' => $risk,
            'score' => $score,
            'reason' => $this->buildReason($local, $ai, $flagged),
            'matched_terms' => $local['matched_terms'],
            'matched_rules' => $matchedRules,
            'engine' => implode('+', $engine),

            'ai_flagged' => (bool) $ai['flagged'],
            'ai_decision' => $ai['decision'],
            'ai_policy_category' => $ai['policy_category'],
            'ai_confidence' => (int) $ai['confidence'],
            'ai_reason' => $ai['reason'],
            'ai_signals' => $ai['signals'],
            'ai_image_reviewed' => (bool) $ai['image_reviewed'],
            'ai_text_reviewed' => (bool) $ai['text_reviewed'],
            'ai_status' => $ai['status'],
            'ai_response_id' => $ai['response_id'],
            'ai_categories' => $ai['policy_category'] !== 'none' ? [$ai['policy_category']] : [],
        ];
    }

    public function record(SellerProduct $product, array $scan, string $source): void
    {
        ProductModerationLog::create([
            'seller_product_id' => $product->id,
            'seller_account_id' => $product->seller_account_id,
            'source' => $source,
            'decision' => $scan['flagged'] ? 'flagged' : 'pending',
            'risk' => $scan['risk'],
            'risk_score' => $scan['score'],
            'engine' => $scan['engine'],
            'matched_rules' => $scan['matched_rules'],
            'matched_terms' => $scan['matched_terms'],
            'ai_flagged' => $scan['ai_flagged'],
            'ai_categories' => $scan['ai_categories'],
            'ai_decision' => $scan['ai_decision'],
            'ai_policy_category' => $scan['ai_policy_category'],
            'ai_confidence' => $scan['ai_confidence'],
            'ai_reason' => $scan['ai_reason'],
            'ai_signals' => $scan['ai_signals'],
            'ai_image_reviewed' => $scan['ai_image_reviewed'],
            'ai_text_reviewed' => $scan['ai_text_reviewed'],
            'ai_status' => $scan['ai_status'],
            'ai_response_id' => $scan['ai_response_id'],
            'reason' => $scan['reason'],
        ]);
    }

    private function scanLocalRules(string $text): array
    {
        $normalized = $this->normalize($text);
        $compact = preg_replace('/[^a-z0-9]+/', '', $normalized) ?? '';

        $matchedRules = [];
        $matchedTerms = [];
        $score = 0;
        $hardFlag = false;

        foreach ((array) config('sari_compliance.rules', []) as $key => $rule) {
            $ruleMatched = false;

            foreach ((array) ($rule['terms'] ?? []) as $term) {
                $termNormalized = $this->normalize((string) $term);
                $termCompact = preg_replace('/[^a-z0-9]+/', '', $termNormalized) ?? '';

                $normalMatch = $this->termMatches($normalized, $termNormalized);
                $compactMatch = $this->obfuscatedTermMatches($normalized, $termCompact);

                if ($normalMatch || $compactMatch) {
                    $ruleMatched = true;
                    $matchedTerms[] = (string) $term;
                }
            }

            if ($ruleMatched) {
                $matchedRules[] = (string) ($rule['label'] ?? $key);
                $score = max($score, (int) ($rule['score'] ?? 50));
                $hardFlag = $hardFlag || (bool) ($rule['hard_flag'] ?? false);
            }
        }

        return [
            'score' => min(100, max(0, $score)),
            'hard_flag' => $hardFlag,
            'matched_rules' => array_values(array_unique($matchedRules)),
            'matched_terms' => array_values(array_unique($matchedTerms)),
        ];
    }

    private function termMatches(string $text, string $term): bool
    {
        if ($term === '') {
            return false;
        }

        $pattern = '/(?<![a-z0-9])' . preg_quote($term, '/') . '(?![a-z0-9])/i';

        return preg_match($pattern, $text) === 1;
    }

    private function obfuscatedTermMatches(string $text, string $compactTerm): bool
    {
        if (strlen($compactTerm) < 4) {
            return false;
        }

        $characters = str_split($compactTerm);
        $pattern = '/(?<![a-z0-9])' . implode('[^a-z0-9]*', array_map(
            fn ($char) => preg_quote($char, '/'),
            $characters
        )) . '(?![a-z0-9])/i';

        return preg_match($pattern, $text) === 1;
    }

    private function normalize(string $value): string
    {
        $value = Str::ascii(Str::lower($value));

        $value = strtr($value, [
            '@' => 'a',
            '$' => 's',
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
        ]);

        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }

    private function buildReason(array $local, array $ai, bool $flagged): string
    {
        $parts = [];

        if (!empty($local['matched_rules'])) {
            $parts[] = 'Local policy signal: ' . implode(', ', $local['matched_rules']) . '.';
        }

        if ($ai['available']) {
            $parts[] = 'SARI Gemini AI Inspector: ' . $ai['reason'];
        } else {
            $parts[] = 'SARI Gemini AI Inspector unavailable (' . $ai['status'] . '); administrator approval is required.';
        }

        if ($flagged) {
            $parts[] = 'Listing was flagged for administrator review. No seller warning or suspension was issued automatically.';
        } else {
            $parts[] = 'Listing remains pending administrator approval before public listing.';
        }

        return implode(' ', $parts);
    }

    private function humanizePolicyCategory(string $value): string
    {
        return match ($value) {
            'weapons' => 'Weapons / dangerous weapon-related merchandise',
            'controlled_substances' => 'Controlled / prohibited substances',
            'hazardous_materials' => 'Hazardous / toxic materials',
            'counterfeit_goods' => 'Possible counterfeit merchandise',
            'fraudulent_documents_services' => 'Fraudulent documents / services',
            'stolen_goods_accounts' => 'Possible stolen goods / accounts',
            'illegal_wildlife' => 'Possible illegal wildlife trade',
            'regulated_restricted' => 'Regulated / restricted merchandise',
            'malicious_or_surveillance_tools' => 'Malicious / surveillance tools',
            'self_harm_enabling_goods' => 'Self-harm enabling merchandise',
            default => 'Other prohibited / restricted merchandise',
        };
    }
}
