$ErrorActionPreference = 'Stop'

$projectRoot = (Get-Location).Path
$serviceRoot = Join-Path $projectRoot 'app\Services'
if (!(Test-Path (Join-Path $projectRoot 'artisan')) -or !(Test-Path $serviceRoot)) {
    throw 'Run this script from the Laravel project root containing artisan.'
}

$groups = [ordered]@{
    AiProductInspector = 'Products'
    BuyerCartService = 'Buyer'
    BuyerCatalogService = 'Buyer'
    BuyerIdentityService = 'Buyer'
    CommissionService = 'Finance'
    FinancialFlowService = 'Finance'
    MarketplaceOrderWorkflowService = 'Orders'
    PaymentLedgerService = 'Finance'
    PlatformMessagingService = 'Messaging'
    PlatformSettingsService = 'Platform'
    ProductComplianceService = 'Products'
    ProductVariantInventoryService = 'Products'
    RegistrationApplicationService = 'Registration'
    RegistrationEmailVerificationService = 'Registration'
    RiderEarningService = 'Finance'
    SariAdminAssistantService = 'Messaging'
    SellerAccountStatusService = 'Seller'
    SellerSettlementService = 'Finance'
    SellerVoucherService = 'Seller'
}

$unknown = @(Get-ChildItem -LiteralPath $serviceRoot -Filter '*.php' -File |
    Where-Object { !$groups.Contains($_.BaseName) })
if ($unknown.Count -gt 0) {
    throw ('Unmapped services found: ' + (($unknown | ForEach-Object { $_.Name }) -join ', ') + '. No files have been changed.')
}

$classMap = @{}
$serviceFiles = @{}
foreach ($name in $groups.Keys) {
    $group = $groups[$name]
    $source = Join-Path $serviceRoot "$name.php"
    $target = Join-Path (Join-Path $serviceRoot $group) "$name.php"
    $hasSource = Test-Path -LiteralPath $source
    $hasTarget = Test-Path -LiteralPath $target

    if ($hasSource -and $hasTarget) {
        throw "Both source and destination exist for $name. No files have been changed."
    }
    if (!$hasSource -and !$hasTarget) {
        throw "Missing service: $name. No files have been changed."
    }

    $current = $source
    if ($hasTarget) { $current = $target }
    $classMap[$name] = "App\Services\$group\$name"
    $serviceFiles[$current] = [PSCustomObject]@{ Group = $group; Target = $target }
}

$names = ($groups.Keys | ForEach-Object { [regex]::Escape($_) }) -join '|'
$referencePattern = [regex]::new('App\\Services\\(?<name>' + $names + ')(?![A-Za-z0-9_\\])')
$rootNamespace = 'namespace App\Services;'
$utf8 = New-Object System.Text.UTF8Encoding($false)
$changes = @()

foreach ($directory in @('app', 'routes', 'tests', 'bootstrap', 'config', 'resources', 'database')) {
    $directoryPath = Join-Path $projectRoot $directory
    if (!(Test-Path $directoryPath)) { continue }

    foreach ($file in Get-ChildItem -LiteralPath $directoryPath -Filter '*.php' -File -Recurse) {
        $original = [System.IO.File]::ReadAllText($file.FullName)
        $updated = $referencePattern.Replace($original, [System.Text.RegularExpressions.MatchEvaluator] {
            param($match)
            return $classMap[$match.Groups['name'].Value]
        })
        $target = $file.FullName

        if ($serviceFiles.ContainsKey($file.FullName)) {
            $record = $serviceFiles[$file.FullName]
            $namespace = "namespace App\Services\$($record.Group);"
            if ($updated.Contains($rootNamespace)) {
                $updated = $updated.Replace($rootNamespace, $namespace)
            } elseif (!$updated.Contains($namespace)) {
                throw "Unexpected namespace in $($file.FullName). No files have been changed."
            }
            # Preserve service dependencies that previously shared the root namespace.
            $imports = @()
            $relatedNames = @($classMap.Keys)
            foreach ($related in $relatedNames) {
                if ($related -eq $file.BaseName) { continue }
                if ($classMap.ContainsKey($related)) {
                    $relatedClass = $classMap[$related]
                    if ($groups[$related] -eq $record.Group) { continue }
                } else {
                    $relatedClass = "App\Services\$related"
                }
                $reference = '(?<![A-Za-z0-9_\\])' + [regex]::Escape($related) + '(?![A-Za-z0-9_\\])'
                $import = "use $relatedClass;"
                if ([regex]::IsMatch($updated, $reference) -and !$updated.Contains($import)) {
                    $imports += $import
                }
            }
            if ($imports.Count -gt 0) {
                $newline = "`n"
                if ($original.Contains("`r`n")) { $newline = "`r`n" }
                $updated = $updated.Replace($namespace, $namespace + $newline + $newline + (($imports | Sort-Object) -join $newline))
            }
            $target = $record.Target
        }

        if ($updated -cne $original -or $target -ne $file.FullName) {
            $changes += [PSCustomObject]@{
                Source = $file.FullName
                Target = $target
                RelativePath = $file.FullName.Substring($projectRoot.TrimEnd('\').Length + 1)
                Content = $updated
                OriginalAttributes = $file.Attributes
            }
        }
    }
}

if ($changes.Count -eq 0) {
    Write-Host 'Service folders, namespaces and references are already updated.'
    return
}

$backupRoot = Join-Path $projectRoot ('storage\app\service-structure-backups\' + [guid]::NewGuid().ToString('N'))
foreach ($change in $changes) {
    $backupPath = Join-Path $backupRoot $change.RelativePath
    New-Item -ItemType Directory -Path (Split-Path $backupPath -Parent) -Force | Out-Null
    Copy-Item -LiteralPath $change.Source -Destination $backupPath
}

function Clear-ReadOnlyAttribute {
    param([string]$Path)
    if (Test-Path -LiteralPath $Path) {
        $attributes = [System.IO.File]::GetAttributes($Path)
        $readOnlyFlag = [int][System.IO.FileAttributes]::ReadOnly
        if (([int]$attributes -band $readOnlyFlag) -ne 0) {
            [System.IO.File]::SetAttributes($Path, [System.IO.FileAttributes]([int]$attributes -band (-bnot $readOnlyFlag)))
        }
    }
}

$operationPath = ''
$operation = ''
try {
    foreach ($change in $changes) {
        $operationPath = $change.Target
        $operation = 'writing the updated file'
        New-Item -ItemType Directory -Path (Split-Path $change.Target -Parent) -Force | Out-Null
        Clear-ReadOnlyAttribute -Path $change.Target
        [System.IO.File]::WriteAllText($change.Target, $change.Content, $utf8)
    }
    foreach ($change in $changes) {
        if ($change.Source -ne $change.Target) {
            $operationPath = $change.Source
            $operation = 'removing the original file after moving it'
            Clear-ReadOnlyAttribute -Path $change.Source
            Remove-Item -LiteralPath $change.Source
        }
    }
    foreach ($change in $changes) {
        $operationPath = $change.Target
        $operation = 'restoring the original file attributes'
        [System.IO.File]::SetAttributes($change.Target, $change.OriginalAttributes)
    }
} catch {
    $originalError = $_.Exception.Message
    $failedPath = $operationPath
    $failedOperation = $operation
    $rollbackErrors = @()
    foreach ($change in $changes) {
        try {
            Clear-ReadOnlyAttribute -Path $change.Source
            Copy-Item -LiteralPath (Join-Path $backupRoot $change.RelativePath) -Destination $change.Source -Force
            [System.IO.File]::SetAttributes($change.Source, $change.OriginalAttributes)
        } catch {
            $rollbackErrors += "Could not restore $($change.Source): $($_.Exception.Message)"
        }
        if ($change.Source -ne $change.Target -and (Test-Path -LiteralPath $change.Target)) {
            try {
                Clear-ReadOnlyAttribute -Path $change.Target
                Remove-Item -LiteralPath $change.Target
            } catch {
                $rollbackErrors += "Could not remove new file $($change.Target): $($_.Exception.Message)"
            }
        }
    }
    Write-Host "Failed while $failedOperation"
    Write-Host "File: $failedPath"
    Write-Host "Original error: $originalError"
    Write-Host "Backup: $backupRoot"
    if ($rollbackErrors.Count -gt 0) {
        foreach ($message in $rollbackErrors) { Write-Host $message }
        throw 'Update failed and rollback needs attention. Keep the backup and send the complete output.'
    }
    throw 'Update failed. Original files were restored. Send the File and Original error lines above.'
}

Write-Host "Updated $($changes.Count) files. Service folders and references are ready."
Write-Host "Backup: $backupRoot"
Write-Host 'Next: composer dump-autoload, then php artisan optimize:clear and php artisan route:list.'
