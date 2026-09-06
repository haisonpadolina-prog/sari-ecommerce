<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressLookupController extends Controller
{
    private string $cloudBase = 'https://psgc.cloud/api/v1';
    private string $gitlabBase = 'https://psgc.gitlab.io/api';

    public function provinces()
    {
        $items = Cache::remember(
            'sari.psgc.provinces.wizard.v1',
            now()->addDay(),
            fn () => $this->fetchFirstAvailable([
                [
                    'url' => $this->cloudBase . '/provinces',
                    'query' => ['per_page' => 200],
                ],
                [
                    'url' => $this->gitlabBase . '/provinces/',
                    'query' => [],
                ],
            ])
        );

        $hasNcr = collect($items)->contains(
            fn ($item) =>
                str_contains(
                    strtolower((string) ($item['name'] ?? '')),
                    'metro manila'
                )
                || (string) ($item['code'] ?? '') === '130000000'
        );

        if (!$hasNcr) {
            $items[] = [
                'code' => '130000000',
                'name' => 'Metro Manila (NCR)',
            ];
        }

        return response()->json($items);
    }

    public function municipalities(string $provinceCode)
    {
        abort_unless(preg_match('/^[0-9]+$/', $provinceCode), 422);

        $items = Cache::remember(
            'sari.psgc.localities.' . $provinceCode . '.wizard.v1',
            now()->addDay(),
            function () use ($provinceCode) {
                if ($provinceCode === '130000000') {
                    return $this->fetchFirstAvailable([
                        [
                            'url' => $this->cloudBase . '/cities-municipalities',
                            'query' => [
                                'region_code' => '130000000',
                                'per_page' => 100,
                            ],
                        ],
                        [
                            'url' => $this->gitlabBase . '/regions/130000000/cities-municipalities/',
                            'query' => [],
                        ],
                    ]);
                }

                return $this->fetchFirstAvailable([
                    [
                        'url' => $this->cloudBase
                            . '/provinces/'
                            . $provinceCode
                            . '/cities-municipalities',
                        'query' => ['per_page' => 300],
                    ],
                    [
                        'url' => $this->gitlabBase
                            . '/provinces/'
                            . $provinceCode
                            . '/cities-municipalities/',
                        'query' => [],
                    ],
                ]);
            }
        );

        return response()->json($items);
    }

    public function barangays(string $municipalityCode)
    {
        abort_unless(preg_match('/^[0-9]+$/', $municipalityCode), 422);

        $items = Cache::remember(
            'sari.psgc.barangays.' . $municipalityCode . '.wizard.v1',
            now()->addDay(),
            fn () => $this->fetchFirstAvailable([
                [
                    'url' => $this->cloudBase
                        . '/cities-municipalities/'
                        . $municipalityCode
                        . '/barangays',
                    'query' => ['per_page' => 1000],
                ],
                [
                    'url' => $this->gitlabBase
                        . '/cities-municipalities/'
                        . $municipalityCode
                        . '/barangays/',
                    'query' => [],
                ],
            ])
        );

        return response()->json($items);
    }

    private function fetchFirstAvailable(array $providers): array
    {
        foreach ($providers as $provider) {
            $response = $this->request(
                $provider['url'],
                $provider['query'] ?? [],
                false
            );

            if ($response && $response->successful()) {
                $items = $this->normalize($response->json());

                if (!empty($items)) {
                    return $items;
                }
            }
        }

        if (app()->environment('local')) {
            foreach ($providers as $provider) {
                $response = $this->request(
                    $provider['url'],
                    $provider['query'] ?? [],
                    true
                );

                if ($response && $response->successful()) {
                    $items = $this->normalize($response->json());

                    if (!empty($items)) {
                        Log::notice(
                            'SARI PSGC used local SSL fallback.',
                            ['url' => $provider['url']]
                        );

                        return $items;
                    }
                }
            }
        }

        Log::error('SARI PSGC providers unavailable.', [
            'providers' => array_column($providers, 'url'),
        ]);

        abort(503, 'Philippine address directory is temporarily unavailable.');
    }

    private function request(
        string $url,
        array $query,
        bool $withoutVerification
    ): ?Response {
        try {
            $request = Http::acceptJson()
                ->withHeaders([
                    'User-Agent' => 'SARI-Laravel/1.0',
                ])
                ->connectTimeout(7)
                ->timeout(15)
                ->retry(1, 250, throw: false);

            if ($withoutVerification) {
                $request = $request->withoutVerifying();
            }

            return $request->get($url, $query);
        } catch (\Throwable $e) {
            Log::warning('SARI PSGC request failed.', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function normalize(mixed $payload): array
    {
        if (!is_array($payload)) {
            return [];
        }

        if (array_is_list($payload)) {
            return $payload;
        }

        foreach (['data', 'results', 'items'] as $key) {
            if (!array_key_exists($key, $payload)) {
                continue;
            }

            $value = $payload[$key];

            if (is_array($value) && array_is_list($value)) {
                return $value;
            }

            if (
                is_array($value)
                && isset($value['data'])
                && is_array($value['data'])
                && array_is_list($value['data'])
            ) {
                return $value['data'];
            }
        }

        return [];
    }
}
