<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SellerShippingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/seller-shipping.php'));

        /*
        |--------------------------------------------------------------------------
        | Seller sidebar integration
        |--------------------------------------------------------------------------
        |
        | The current project keeps the complete Seller sidebar inside
        | resources/views/layouts/seller.blade.php. To avoid replacing that
        | large layout and accidentally overwriting the project's existing UI,
        | this provider inserts one Shipping navigation item when Blade compiles
        | the Seller layout. The source layout remains untouched.
        |
        */
        Blade::precompiler(function (string $value): string {
            $marker = '            {{-- ARCHIVED PRODUCTS --}}';

            if (
                !str_contains($value, $marker)
                || str_contains($value, "route('seller.shipping.index')")
            ) {
                return $value;
            }

            $navigation = <<<'BLADE'
            {{-- SHIPPING / TRACKING --}}
            <a
                href="{{ route('seller.shipping.index') }}"
                title="Shipping Tracking"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.shipping.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 7h11v10H3z"></path>
                    <path d="M14 10h3l4 4v3h-7z"></path>
                    <circle cx="7" cy="18" r="1.8"></circle>
                    <circle cx="18" cy="18" r="1.8"></circle>
                    <path d="M5 11h6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Shipping
                </span>
            </a>
BLADE;

            return str_replace(
                $marker,
                $navigation . "\n\n" . $marker,
                $value
            );
        });
    }
}
