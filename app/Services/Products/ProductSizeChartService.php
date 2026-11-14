<?php

namespace App\Services\Products;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductSizeChartService
{
    public static function dimensions(string $type): array
    {
        return match ($type) {
            'bottom' => ['waist', 'hips'],
            'dress' => ['chest', 'waist', 'hips'],
            'footwear' => ['foot'],
            default => ['chest'],
        };
    }

    public static function sizeKey(string $value): string
    {
        $value = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $value)));
        return ['extra small'=>'xs','small'=>'s','medium'=>'m','large'=>'l','extra large'=>'xl','extra-large'=>'xl','2xl'=>'xxl','extra extra large'=>'xxl'][$value] ?? $value;
    }

    public function validate(array $input, array $sizeLabels): array
    {
        $rules = [
            'unit' => ['required', Rule::in(['cm','in'])],
            'measurement_type' => ['required', Rule::in(['body_range','garment'])],
            'garment_type' => ['required', Rule::in(['top','bottom','dress','footwear'])],
            'fit' => ['required', Rule::in(['regular','slim','relaxed'])],
            'rows' => ['required','array','min:1','max:40'],
            'rows.*' => ['required','array:size,measurements'],
            'rows.*.size' => ['required','string','max:40'],
            'rows.*.measurements' => ['required','array:chest,waist,hips,foot'],
        ];
        foreach (['chest','waist','hips','foot'] as $dimension) {
            $base = 'rows.*.measurements.'.$dimension;
            $rules[$base] = ['sometimes','array:min,max,ease_min,ease_max'];
            $rules[$base.'.min'] = ['required_with:'.$base,'numeric','min:0.1','max:300'];
            $rules[$base.'.max'] = ['required_with:'.$base,'numeric','min:0.1','max:300'];
            $rules[$base.'.ease_min'] = ['nullable','numeric','min:0','max:50'];
            $rules[$base.'.ease_max'] = ['nullable','numeric','min:0','max:50'];
        }
        $validator = Validator::make($input, $rules);
        $validator->after(function ($validator) use ($input, $sizeLabels) {
            if ($validator->errors()->isNotEmpty()) return;
            $allowed = array_map([self::class,'sizeKey'], $sizeLabels);
            $seen = [];
            foreach ($input['rows'] as $i => $row) {
                $key = self::sizeKey($row['size']);
                if ($key === '' || !in_array($key, $allowed, true)) $validator->errors()->add('rows.'.$i.'.size', 'Use a size label from this product’s active variants.');
                if (in_array($key, $seen, true)) $validator->errors()->add('rows.'.$i.'.size', 'Each size must appear only once.');
                $seen[] = $key;
                $required = self::dimensions($input['garment_type']);
                foreach ($required as $dimension) {
                    if (!isset($row['measurements'][$dimension])) $validator->errors()->add('rows.'.$i.'.measurements', 'Each row needs '.implode(', ', $required).' measurements.');
                }
                foreach ($row['measurements'] as $dimension => $range) {
                    if (!in_array($dimension,$required,true)) $validator->errors()->add('rows.'.$i.'.measurements','Use only '.implode(', ',$required).' for this product type.');
                    if ((float) $range['min'] > (float) $range['max']) $validator->errors()->add('rows.'.$i.'.measurements.'.$dimension, 'Minimum cannot exceed maximum.');
                    if ($input['measurement_type'] === 'garment') {
                        if (!isset($range['ease_min'], $range['ease_max']) || (float) $range['ease_min'] > (float) $range['ease_max']) $validator->errors()->add('rows.'.$i.'.measurements.'.$dimension, 'Garment charts require a valid seller-defined minimum and maximum ease.');
                    }
                }
            }
        });
        $validated = $validator->validate();
        // Persist only known fields, trim labels, and make JSON numeric types explicit.
        $validated['rows'] = array_map(function ($row) use ($validated, $sizeLabels) {
            $measurements = [];
            foreach ($row['measurements'] as $name => $range) {
                $measurements[$name] = ['min'=>(float)$range['min'], 'max'=>(float)$range['max']];
                if ($validated['measurement_type'] === 'garment') $measurements[$name] += ['ease_min'=>(float)$range['ease_min'], 'ease_max'=>(float)$range['ease_max']];
            }
            return ['size'=>trim(collect($sizeLabels)->first(fn ($label) => self::sizeKey($label) === self::sizeKey($row['size']))), 'measurements'=>$measurements];
        }, $validated['rows']);
        return $validated;
    }

    /** No guessed ease, label order, or height/weight conversion. */
    public function match(array $chart, array $body, array $variants, ?string $preferredFit = null): array
    {
        if (!empty($body['_issues'])) return ['status'=>'clarification_needed','issues'=>$body['_issues']];
        $required = self::dimensions((string) ($chart['garment_type'] ?? 'top'));
        $missing = array_values(array_filter($required, fn ($key) => !isset($body[$key.'_cm'])));
        if ($missing) return ['status'=>'measurements_needed','missing'=>array_map(fn ($key) => $key.'_cm', $missing)];
        if ($preferredFit && $preferredFit !== ($chart['fit'] ?? '')) return ['status'=>'fit_mismatch'];
        $factor = ($chart['unit'] ?? 'cm') === 'in' ? 2.54 : 1;
        $matches = [];
        foreach ($chart['rows'] ?? [] as $row) {
            $evidence = [];
            foreach ($required as $dimension) {
                $range = $row['measurements'][$dimension] ?? null;
                if (!$range) continue 2;
                $min = (float) $range['min'] * $factor; $max = (float) $range['max'] * $factor;
                $value = (float) $body[$dimension.'_cm'];
                if (($chart['measurement_type'] ?? '') === 'body_range') {
                    if ($value < $min - .005 || $value > $max + .005) continue 2;
                    $evidence[] = ucfirst($dimension).' '.$value.' cm is within '.$min.'–'.$max.' cm';
                } elseif (($chart['measurement_type'] ?? '') === 'garment') {
                    if (!isset($range['ease_min'], $range['ease_max'])) continue 2;
                    $easeMin = (float) $range['ease_min'] * $factor; $easeMax = (float) $range['ease_max'] * $factor;
                    if ($min-$value < $easeMin-.005 || $max-$value > $easeMax+.005) continue 2;
                    $evidence[] = ucfirst($dimension).' '.$value.' cm + seller ease '.$easeMin.'–'.$easeMax.' cm; garment '.$min.'–'.$max.' cm';
                } else continue 2;
            }
            $matches[] = ['row'=>$row, 'evidence'=>$evidence];
        }
        if (!$matches) return ['status'=>'no_match'];
        if (count($matches) > 1) return ['status'=>'ambiguous','sizes'=>array_column(array_column($matches,'row'),'size')];
        $matched = $matches[0];
        $variant = collect($variants)->first(fn ($variant) => (int) ($variant['stock'] ?? 0)>0 && self::sizeKey((string) ($variant['size'] ?? '')) === self::sizeKey($matched['row']['size']));
        if (!$variant) return ['status'=>'size_unavailable','size'=>$matched['row']['size']];
        return ['status'=>'matched','variant'=>$variant,'evidence'=>$matched['evidence'],'fit'=>$chart['fit']];
    }
}
