param(
    [string]$ProjectRoot = (Get-Location).Path
)

$ErrorActionPreference = 'Stop'

function Write-Step([string]$Message) {
    Write-Host ""
    Write-Host "==> $Message" -ForegroundColor Cyan
}

$sourceRoot = (Resolve-Path $PSScriptRoot).Path
$projectRootResolved = (Resolve-Path $ProjectRoot).Path
$patchPath = Join-Path $sourceRoot 'sari-admin-missing-security-functions-existing-files.patch'

if (-not (Test-Path $patchPath)) {
    throw "Patch file is missing from the installer folder: $patchPath"
}

$newFiles = @(
    'app\Http\Controllers\Admin\AdminSecurityCenterController.php',
    'app\Http\Middleware\Platform\RecordSecurityEvents.php',
    'app\Services\Admin\AdminSecurityCenterService.php',
    'resources\views\admin\security-center.blade.php',
    'resources\views\admin\partials\security-alert-overlay.blade.php',
    'routes\admin-security.php',
    'tests\Feature\AdminSecurityCenterDeltaTest.php'
)

$existingFilesTouchedByPatch = @(
    'bootstrap\app.php',
    'routes\web.php',
    'resources\views\components\admin\sidebar.blade.php',
    'resources\views\layouts\admin.blade.php',
    'app\Http\Controllers\Admin\AdminUsersController.php',
    'app\Services\Admin\AdminPlatformMonitoringService.php',
    'resources\views\admin\monitoring\detail.blade.php'
)

Write-Step "Checking Laravel project root"
if (-not (Test-Path (Join-Path $projectRootResolved 'artisan'))) {
    throw "artisan was not found in: $projectRootResolved"
}

Write-Step "Copying only the new Security Center files"
foreach ($relative in $newFiles) {
    $source = Join-Path $sourceRoot $relative
    $target = Join-Path $projectRootResolved $relative

    if (-not (Test-Path $source)) {
        throw "Installer source file is missing: $relative"
    }

    $targetDirectory = Split-Path $target -Parent
    if (-not (Test-Path $targetDirectory)) {
        New-Item -ItemType Directory -Path $targetDirectory -Force | Out-Null
    }

    # Avoid Copy-Item error when the ZIP was extracted directly into project root.
    if ((Resolve-Path $source).Path -ne (Resolve-Path $target -ErrorAction SilentlyContinue).Path) {
        Copy-Item $source $target -Force
    }
}

Push-Location $projectRootResolved
try {
    Write-Step "Checking whether the small integration patch is needed"

    & git apply --check --whitespace=nowarn $patchPath 2>$null
    $canApply = ($LASTEXITCODE -eq 0)

    if ($canApply) {
        $stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
        $backupRoot = Join-Path $projectRootResolved ".security-delta-backup-$stamp"

        Write-Step "Creating a safety backup of only the 7 existing files that will be touched"
        foreach ($relative in $existingFilesTouchedByPatch) {
            $source = Join-Path $projectRootResolved $relative
            if (Test-Path $source) {
                $backup = Join-Path $backupRoot $relative
                New-Item -ItemType Directory -Path (Split-Path $backup -Parent) -Force | Out-Null
                Copy-Item $source $backup -Force
            }
        }

        Write-Step "Applying Security Center integration hooks"
        & git apply --whitespace=nowarn $patchPath
        if ($LASTEXITCODE -ne 0) {
            throw "git apply failed. Your backup is at: $backupRoot"
        }

        Write-Host "Integration patch applied." -ForegroundColor Green
        Write-Host "Backup: $backupRoot"
    }
    else {
        & git apply --reverse --check --whitespace=nowarn $patchPath 2>$null
        $alreadyApplied = ($LASTEXITCODE -eq 0)

        if ($alreadyApplied) {
            Write-Host "Integration patch is already applied. No duplicate changes were made." -ForegroundColor Yellow
        }
        else {
            throw @"
The integration patch does not match your current local versions of the 7 existing files.
Nothing was patched.

Do NOT force-apply it.
Run these commands and send the output:
  git status
  git diff -- bootstrap/app.php routes/web.php resources/views/components/admin/sidebar.blade.php resources/views/layouts/admin.blade.php app/Http/Controllers/Admin/AdminUsersController.php app/Services/Admin/AdminPlatformMonitoringService.php resources/views/admin/monitoring/detail.blade.php
"@
        }
    }

    Write-Step "Clearing Laravel caches"
    & php artisan optimize:clear
    if ($LASTEXITCODE -ne 0) {
        throw "php artisan optimize:clear failed."
    }

    Write-Step "Verifying Security Center routes"
    & php artisan route:list --name=admin.security
    if ($LASTEXITCODE -ne 0) {
        throw "Security routes are still not registered."
    }

    Write-Host ""
    Write-Host "Installation step completed." -ForegroundColor Green
    Write-Host "Now run:"
    Write-Host "php artisan test tests/Feature/AdminSecurityCenterDeltaTest.php"
    Write-Host "php artisan test tests/Feature/AdminPlatformMonitoringTest.php"
}
finally {
    Pop-Location
}
