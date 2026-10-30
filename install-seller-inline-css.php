<?php
/**
 * SARI Seller: embed each Seller stylesheet in its owning Blade view.
 *
 * Run from project root: php install-seller-inline-css.php
 * Optional: php install-seller-inline-css.php --dry-run
 *
 * This script reads the CURRENT public/css/seller/*.css files and modifies
 * only their corresponding Seller Blade <link> tags. Safe to rerun.
 * Existing files are backed up before any writes.
 */

declare(strict_types=1);

$root = __DIR__;
if (!is_file($root.'/artisan') || !is_dir($root.'/resources/views')) {
    fwrite(STDERR, "ERROR: Put this installer next to artisan in your Laravel project root.\n");
    exit(1);
}

$dryRun = in_array('--dry-run', $argv, true);
$markerPattern = '~<style\b(?=[^>]*\bdata-sari-inline-css=["\']([a-z0-9-]+)["\'])[^>]*>.*?</style>~is';
$linkPattern = '~<link\b[^>]*>~is';
$changed = [];
$issues = [];
$stylesheets = [];
$expectedSheets = [];

/** No visual CSS rewriting. Only newlines are normalized in the embedded copy. */
function inlineSellerCss(string $root, string $slug, string $lineEnding, array &$issues): ?string
{
    if (!preg_match('/\A[a-z0-9-]+\z/', $slug)) {
        $issues[] = "Invalid stylesheet name: {$slug}";
        return null;
    }
    $path = $root.'/public/css/seller/'.$slug.'.css';
    if (!is_file($path) || !is_readable($path)) {
        $issues[] = "Missing/unreadable CSS: public/css/seller/{$slug}.css";
        return null;
    }
    $css = file_get_contents($path);
    if ($css === false) {
        $issues[] = "Could not read CSS: {$path}";
        return null;
    }
    if (preg_match('~</style\s*>|@endverbatim\b~i', $css)) {
        $issues[] = "Inline-unsafe CSS content: {$slug}.css";
        return null;
    }
    $css = str_replace(["\r\n", "\r"], "\n", $css);
    $out = '<style id="sariSellerEmbeddedCss-'.$slug.'" data-sari-inline-css="'.$slug.'">'
        ."\n@verbatim\n".rtrim($css, "\n")."\n@endverbatim\n</style>";
    return $lineEnding === "\r\n" ? str_replace("\n", "\r\n", $out) : $out;
}

function routeCssSlug(string $link): ?string
{
    if (!preg_match('~\brel\s*=\s*["\']stylesheet["\']~i', $link)) return null;
    if (!preg_match('~asset\(\s*["\']css/seller/([a-z0-9-]+)\.css["\']\s*\)~i', $link, $m)) return null;
    return strtolower($m[1]);
}

function putChange(string $file, string $old, string $new, array &$changed): void
{
    if ($new !== $old) $changed[$file] = [$old, $new];
}

$scan = [
    $root.'/resources/views/layouts/seller.blade.php',
];
$viewFolder = $root.'/resources/views/seller';
if (is_dir($viewFolder)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewFolder, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $item) {
        if ($item->isFile() && str_ends_with($item->getFilename(), '.blade.php')) {
            $scan[] = $item->getPathname();
        }
    }
}

foreach ($scan as $file) {
    if (!is_file($file)) continue;
    $original = file_get_contents($file);
    if ($original === false) {
        $issues[] = 'Could not read '.substr($file, strlen($root) + 1);
        continue;
    }
    $nl = strpos($original, "\r\n") !== false ? "\r\n" : "\n";
    $relative = str_replace('\\', '/', substr($file, strlen($root) + 1));
    $updated = $original;

    // Update an existing embedded block if the source CSS changed: idempotent.
    $updated = preg_replace_callback($markerPattern, function (array $match) use ($root, $nl, &$issues, &$stylesheets): string {
        $slug = strtolower($match[1]);
        $out = inlineSellerCss($root, $slug, $nl, $issues);
        if ($out !== null) $stylesheets[$slug] = true;
        return $out ?? $match[0];
    }, $updated);

    // Replace only Seller CSS <link rel="stylesheet">. Other CSS untouched.
    $updated = preg_replace_callback($linkPattern, function (array $match) use ($root, $nl, &$issues, &$stylesheets): string {
        $slug = routeCssSlug($match[0]);
        if ($slug === null) return $match[0];
        $out = inlineSellerCss($root, $slug, $nl, $issues);
        if ($out !== null) $stylesheets[$slug] = true;
        return $out ?? $match[0];
    }, $updated);

    // Seller's hover CSS warming is redundant after every stylesheet is embedded.
    // Its Javascript prefetch (data-sari-navigation-script) remains untouched.
    if (str_ends_with($relative, 'resources/views/layouts/seller.blade.php')) {
        $updated = preg_replace('~\s+data-sari-navigation-style\s*=\s*"[^"]*"~i', '', $updated);
    }

    putChange($file, $original, $updated, $changed);
}

// The login screen used to prefetch Seller CSS before visiting the dashboard.
// Embedded styles make those two prefetches duplicate downloads.
$loginFile = $root.'/resources/views/pages/login.blade.php';
if (is_file($loginFile)) {
    $original = file_get_contents($loginFile);
    if ($original !== false) {
        $updated = preg_replace_callback($linkPattern, function(array $match): string {
            if (preg_match('~\brel\s*=\s*["\']prefetch["\']~i', $match[0])
                && preg_match('~asset\(\s*["\']css/seller/[a-z0-9-]+\.css["\']\s*\)~i', $match[0])) {
                return '';
            }
            return $match[0];
        }, $original);
        putChange($loginFile, $original, $updated, $changed);
    }
}

if ($issues) {
    foreach ($issues as $problem) fwrite(STDERR, "ERROR: {$problem}\n");
    fwrite(STDERR, "No files changed.\n");
    exit(1);
}

if (!$changed) {
    echo "No changes needed. Seller stylesheets are already embedded and up to date.\n";
    exit(0);
}

if ($dryRun) {
    echo "Dry run: would change ".count($changed)." Blade files. No files written.\n";
    foreach (array_keys($changed) as $file) echo "  ".substr($file, strlen($root) + 1)."\n";
    exit(0);
}

$backup = $root.'/.sari-seller-inline-backup-'.date('Ymd-His').'-'.getmypid();
foreach ($changed as $file => [$before]) {
    $relative = substr($file, strlen($root) + 1);
    $destination = $backup.'/'.$relative;
    if (!is_dir(dirname($destination)) && !mkdir(dirname($destination), 0755, true)) {
        fwrite(STDERR, "ERROR: Could not create backup folder {$destination}; no changes written.\n");
        exit(1);
    }
    if (file_put_contents($destination, $before) !== strlen($before)) {
        fwrite(STDERR, "ERROR: Backup failure for {$relative}; no changes written.\n");
        exit(1);
    }
}
foreach ($changed as $file => [$before, $after]) {
    if (file_put_contents($file, $after, LOCK_EX) !== strlen($after)) {
        fwrite(STDERR, "ERROR: Failed writing {$file}; restore from {$backup}.\n");
        exit(1);
    }
}

echo "Seller inline CSS installed successfully.\n";
echo 'Embedded Seller CSS files: '.count($stylesheets)."\n";
echo 'Modified Blade files: '.count($changed)."\n";
echo "Originals backed up at: {$backup}\n";
echo "Next: php artisan view:clear\n";
