<?php

namespace App\Services\Buyer;

use App\Models\Platform\BuyerHomepageBanner;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Six fixed positions: only their content and text/button positions are editable.
 * Defaults reproduce the current buyer homepage when no records are published yet.
 */
class BuyerHomepageBannerService
{
    // Suggestions only. Admins may also enter a validated custom family name.
    public const FONTS = [
        'poppins' => "'Poppins', sans-serif",
        'inter' => "'Inter', sans-serif",
        'montserrat' => "'Montserrat', sans-serif",
        'roboto' => "'Roboto', sans-serif",
        'open sans' => "'Open Sans', sans-serif",
        'lato' => "'Lato', sans-serif",
        'nunito' => "'Nunito', sans-serif",
        'raleway' => "'Raleway', sans-serif",
        'oswald' => "'Oswald', sans-serif",
        'playfair display' => "'Playfair Display', serif",
        'merriweather' => "'Merriweather', serif",
        'dm sans' => "'DM Sans', sans-serif",
        'outfit' => "'Outfit', sans-serif",
        'manrope' => "'Manrope', sans-serif",
        'work sans' => "'Work Sans', sans-serif",
        'ubuntu' => "'Ubuntu', sans-serif",
        'rubik' => "'Rubik', sans-serif",
        'libre baskerville' => "'Libre Baskerville', serif",
        'bebas neue' => "'Bebas Neue', sans-serif",
        'space grotesk' => "'Space Grotesk', sans-serif",
        'cormorant garamond' => "'Cormorant Garamond', serif",
        'pt serif' => "'PT Serif', serif",
        'source sans 3' => "'Source Sans 3', sans-serif",
        'georgia' => 'Georgia, serif',
        'arial' => 'Arial, sans-serif',
        'times new roman' => "'Times New Roman', serif",
        'verdana' => 'Verdana, sans-serif',
        'tahoma' => 'Tahoma, sans-serif',
        'trebuchet ms' => "'Trebuchet MS', sans-serif",
        'courier new' => "'Courier New', monospace",
    ];

    /** Only CSS-safe family names are ever interpolated into inline styles or font URLs. */
    public static function safeFontName(mixed $font): string
    {
        $font = trim((string) $font);

        return preg_match_all('/./us', $font) <= 32 && preg_match('/\A[\p{L}\p{N}][\p{L}\p{N} \-]{0,31}\z/u', $font)
            ? $font : 'poppins';
    }

    public static function fontCss(mixed $font): string
    {
        $name = self::safeFontName($font);
        $key = strtolower($name);

        return self::FONTS[$key] ?? "'{$name}', sans-serif";
    }

    public static function googleFontUrl(mixed $font): ?string
    {
        $name = self::safeFontName($font);
        $key = strtolower($name);
        if (in_array($key, ['georgia', 'arial', 'times new roman', 'verdana', 'tahoma', 'trebuchet ms', 'courier new'], true)) {
            return null;
        }

        // Fonts not served by Google Fonts gracefully fall back to installed fonts.
        return 'https://fonts.googleapis.com/css2?family=' . str_replace('%20', '+', rawurlencode($name)) . '&display=swap';
    }

    public static function defaults(): array
    {
        $hero = [
            'button_url' => '/buyer/products', 'font_family' => 'poppins',
            'title_size' => 30, 'description_size' => 11, 'button_size' => 11,
            'text_color' => '#FFFFFF', 'button_bg' => '#C88912', 'button_color' => '#FFFFFF',
            'text_x' => 6, 'text_y' => 42, 'button_x' => 6, 'button_y' => 70,
        ];
        $side = [
            'kicker' => '', 'button_text' => '', 'button_url' => '/buyer/products',
            'font_family' => 'poppins', 'title_size' => 15, 'description_size' => 10,
            'button_size' => 10, 'text_color' => '#FFFFFF', 'button_bg' => '#C88912',
            'button_color' => '#FFFFFF', 'text_x' => 5, 'text_y' => 79,
            'button_x' => 5, 'button_y' => 90,
        ];

        return [
            'hero_1' => array_merge($hero, [
                'image_path' => 'images/hero.jpg', 'kicker' => 'Featured collection',
                'title' => 'Modern picks for everyday living.',
                'description' => 'Curated essentials from trusted SARI sellers.',
                'button_text' => 'Shop collection',
            ]),
            'hero_2' => array_merge($hero, [
                'image_path' => 'images/beauty-bestsellers.png', 'kicker' => 'Beauty edit',
                'title' => 'Everyday beauty, elevated.',
                'description' => 'Polished essentials from trusted SARI sellers.',
                'button_text' => 'Shop beauty', 'text_color' => '#171A1F',
                'text_x' => 6, 'text_y' => 42,
            ]),
            'hero_3' => array_merge($hero, [
                'image_path' => 'images/home-essentials.png', 'kicker' => 'Home refresh',
                'title' => 'Comfort for every corner.',
                'description' => 'Simple finds for a calmer everyday space.',
                'button_text' => 'Explore home', 'text_color' => '#171A1F',
            ]),
            'hero_4' => array_merge($hero, [
                'image_path' => 'images/weekend-watch.png', 'kicker' => 'Style spotlight',
                'title' => 'Timeless details, modern style.',
                'description' => 'Refined accessories from trusted SARI sellers.',
                'button_text' => 'Discover more', 'text_color' => '#171A1F',
            ]),
            'side_beauty' => array_merge($side, [
                'image_path' => 'images/cat-beauty.jpg', 'title' => 'Beauty bestsellers',
                'description' => 'Everyday favorites from trusted sellers.',
            ]),
            'side_flash' => array_merge($side, [
                'image_path' => 'images/sari-ticket-08-product-camera.jpg',
                'title' => 'Flash sale', 'description' => 'Limited-time tech and lifestyle deals.',
                'button_url' => '/buyer/products?discounted=1',
            ]),
        ];
    }

    public function banners(): array
    {
        $banners = self::defaults();

        // The buyer homepage remains usable before migration or during a rollback.
        if (!Schema::hasTable('buyer_homepage_banners')) {
            return $this->withImageUrls($banners);
        }

        foreach (BuyerHomepageBanner::query()->get() as $record) {
            if (!isset($banners[$record->slot])) {
                continue;
            }

            $banners[$record->slot] = array_merge(
                $banners[$record->slot],
                $record->only(array_keys($banners[$record->slot]))
            );
        }

        return $this->withImageUrls($banners);
    }

    private function withImageUrls(array $banners): array
    {
        foreach ($banners as $slot => &$banner) {
            $banner['slot'] = $slot;
            $banner['label'] = match ($slot) {
                'hero_1' => 'Main banner · Slide 1',
                'hero_2' => 'Main banner · Slide 2',
                'hero_3' => 'Main banner · Slide 3',
                'hero_4' => 'Main banner · Slide 4',
                'side_beauty' => 'Right banner · Top',
                'side_flash' => 'Right banner · Bottom',
            };
            $path = (string) $banner['image_path'];
            $banner['image_url'] = str_starts_with($path, 'buyer-homepage/')
                ? Storage::disk('public')->url($path)
                : asset($path);
            $banner['font_css'] = self::fontCss($banner['font_family']);
            $banner['font_url'] = self::googleFontUrl($banner['font_family']);
            $hex = ltrim((string) $banner['text_color'], '#');
            $banner['dark_copy'] = strlen($hex) === 6
                && (hexdec(substr($hex, 0, 2)) + hexdec(substr($hex, 2, 2)) + hexdec(substr($hex, 4, 2))) < 420;
        }
        unset($banner);

        return $banners;
    }
}
