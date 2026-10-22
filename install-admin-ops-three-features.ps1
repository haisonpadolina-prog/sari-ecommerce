param(
    [string]$ProjectRoot = (Get-Location).Path
)

$ErrorActionPreference = "Stop"
$ProjectRoot = (Resolve-Path $ProjectRoot).Path

Write-Host "==> Checking Laravel project root"
if (-not (Test-Path (Join-Path $ProjectRoot "artisan"))) {
    throw "artisan not found in $ProjectRoot"
}

$PatchPath = Join-Path $ProjectRoot "sari-admin-announcements-controls-sla-existing-files.patch"
if (-not (Test-Path $PatchPath)) {
    throw "Integration patch not found: $PatchPath"
}

$FilesToBackup = @(
    "routes\\web.php",
    "resources\\views\\components\\admin\\sidebar.blade.php",
    "resources\\views\\layouts\\buyer.blade.php",
    "resources\\views\\layouts\\seller.blade.php",
    "resources\\views\\layouts\\logistics.blade.php",
    "resources\\views\\layouts\\courier.blade.php",
    "app\\Services\\Platform\\PlatformSettingsService.php",
    "app\\Http\\Controllers\\Admin\\AdminPlatformSettingsController.php",
    "resources\\views\\admin\\platform-settings.blade.php",
    "app\\Http\\Middleware\\Platform\\EnforcePlatformOperationalRules.php",
    "app\\Services\\Promotions\\PlatformCampaignService.php",
    "app\\Http\\Controllers\\Admin\\AdminPlatformCampaignController.php",
    "resources\\views\\admin\\platform-campaigns.blade.php",
    "app\\Services\\Admin\\AdminSecurityCenterService.php",
    "app\\Http\\Controllers\\Admin\\AdminPlatformMonitoringController.php",
    "resources\\views\\admin\\monitoring\\index.blade.php"
)

Write-Host "==> Checking integration patch"
Push-Location $ProjectRoot
try {
    git apply --check $PatchPath 2>$null
    $patchNeeded = ($LASTEXITCODE -eq 0)

    if (-not $patchNeeded) {
        git apply --reverse --check $PatchPath 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Host "Integration patch is already applied."
            exit 0
        }

        throw "Patch does not match the current local files. No force-apply was attempted. Restore/upload the latest affected files for a safe merge."
    }

    $stamp = Get-Date -Format "yyyyMMdd-HHmmss"
    $backupRoot = Join-Path $ProjectRoot ".admin-ops-three-features-backup-$stamp"

    Write-Host "==> Backing up only existing files that will be modified"
    foreach ($relative in $FilesToBackup) {
        $source = Join-Path $ProjectRoot $relative
        if (-not (Test-Path $source)) {
            throw "Expected existing file is missing: $relative"
        }
        $destination = Join-Path $backupRoot $relative
        New-Item -ItemType Directory -Force -Path (Split-Path $destination) | Out-Null
        Copy-Item $source $destination -Force
    }

    Write-Host "==> Applying duplicate-aware integration patch"
    git apply $PatchPath
    if ($LASTEXITCODE -ne 0) {
        throw "git apply failed. Backup is at $backupRoot"
    }

    Write-Host ""
    Write-Host "Integration patch applied."
    Write-Host "Backup: $backupRoot"
    Write-Host ""
    Write-Host "IMPORTANT: Database migration was NOT run automatically."
    Write-Host "Preview it first:"
    Write-Host "php artisan migrate --pretend --path=database/migrations/2026_10_16_235500_create_platform_announcements.php"
    Write-Host "Then apply only that migration:"
    Write-Host "php artisan migrate --path=database/migrations/2026_10_16_235500_create_platform_announcements.php"
    Write-Host "Then:"
    Write-Host "php artisan optimize:clear"
}
finally {
    Pop-Location
}
