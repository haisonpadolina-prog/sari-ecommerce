$ErrorActionPreference = 'Stop'

$projectRoot = (Get-Location).Path
$modelRoot = Join-Path $projectRoot 'app\Models'
if (!(Test-Path (Join-Path $projectRoot 'artisan')) -or !(Test-Path $modelRoot)) {
    throw 'Run this script from the Laravel project root containing artisan.'
}

$groups = [ordered]@{
    Address = 'Shared'
    AdminAccount = 'Accounts'
    AdminUserActivity = 'Platform'
    BuyerAccount = 'Accounts'
    BuyerCartItem = 'Orders'
    BuyerSellerMessage = 'Messaging'
    CartItem = 'Orders'
    Category = 'Catalog'
    ChatMessage = 'Messaging'
    ChatMessageReaction = 'Messaging'
    CommissionAdjustment = 'Finance'
    CommissionAuditLog = 'Finance'
    CommissionRate = 'Finance'
    ComplianceMessage = 'Compliance'
    CourierAccount = 'Accounts'
    DeliveryEvent = 'Delivery'
    LogisticsAccount = 'Accounts'
    LogisticsMessage = 'Messaging'
    LogisticsParcel = 'Delivery'
    LogisticsProvider = 'Delivery'
    MarketplaceOrder = 'Orders'
    MarketplaceOrderEvent = 'Orders'
    Order = 'Orders'
    OrderCommission = 'Finance'
    OrderItem = 'Orders'
    Payment = 'Finance'
    PaymentTransaction = 'Finance'
    PlatformComplaint = 'Platform'
    PlatformConversation = 'Messaging'
    PlatformConversationParticipant = 'Messaging'
    PlatformMessage = 'Messaging'
    PlatformMessageReaction = 'Messaging'
    PlatformSetting = 'Platform'
    PlatformSettingAudit = 'Platform'
    PlatformSettingVersion = 'Platform'
    Product = 'Catalog'
    ProductImage = 'Catalog'
    ProductModerationLog = 'Catalog'
    ProductReview = 'Catalog'
    ProductVariant = 'Catalog'
    RegistrationApplication = 'Registration'
    Rider = 'Delivery'
    RiderEarning = 'Finance'
    RiderPayoutRequest = 'Finance'
    RiderPayoutRequestItem = 'Finance'
    Role = 'Accounts'
    Seller = 'Accounts'
    SellerAccount = 'Accounts'
    SellerChatModerationAction = 'Messaging'
    SellerChatRestriction = 'Messaging'
    SellerInventoryMovement = 'Catalog'
    SellerNotification = 'Platform'
    SellerOrder = 'Orders'
    SellerProduct = 'Catalog'
    SellerProductDraft = 'Catalog'
    SellerProductImage = 'Catalog'
    SellerProductOption = 'Catalog'
    SellerProductOptionValue = 'Catalog'
    SellerProductSpecification = 'Catalog'
    SellerProductVariant = 'Catalog'
    SellerProductVersion = 'Catalog'
    SellerReturnEvent = 'Orders'
    SellerReturnRequest = 'Orders'
    SellerReviewReply = 'Catalog'
    SellerSettlement = 'Finance'
    SellerVoucher = 'Promotions'
    SellerVoucherRedemption = 'Promotions'
    SellerWarning = 'Compliance'
    Shipment = 'Delivery'
    SocialAccount = 'Accounts'
}

$unknown = @(Get-ChildItem -LiteralPath $modelRoot -Filter '*.php' -File |
    Where-Object { $_.BaseName -ne 'User' -and !$groups.Contains($_.BaseName) })
if ($unknown.Count -gt 0) {
    throw ('Unmapped models found: ' + (($unknown | ForEach-Object { $_.Name }) -join ', ') + '. No files have been changed.')
}

$classMap = @{}
$modelFiles = @{}
foreach ($name in $groups.Keys) {
    $group = $groups[$name]
    $source = Join-Path $modelRoot "$name.php"
    $target = Join-Path (Join-Path $modelRoot $group) "$name.php"
    $hasSource = Test-Path -LiteralPath $source
    $hasTarget = Test-Path -LiteralPath $target

    if ($hasSource -and $hasTarget) {
        throw "Both source and destination exist for $name. No files have been changed."
    }
    if (!$hasSource -and !$hasTarget) {
        throw "Missing model: $name. No files have been changed."
    }

    $current = $source
    if ($hasTarget) { $current = $target }
    $classMap[$name] = "App\Models\$group\$name"
    $modelFiles[$current] = [PSCustomObject]@{ Group = $group; Target = $target }
}

$names = ($groups.Keys | ForEach-Object { [regex]::Escape($_) }) -join '|'
$referencePattern = [regex]::new('App\\Models\\(?<name>' + $names + ')(?![A-Za-z0-9_\\])')
$rootNamespace = 'namespace App\Models;'
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

        if ($modelFiles.ContainsKey($file.FullName)) {
            $record = $modelFiles[$file.FullName]
            $namespace = "namespace App\Models\$($record.Group);"
            if ($updated.Contains($rootNamespace)) {
                $updated = $updated.Replace($rootNamespace, $namespace)
            } elseif (!$updated.Contains($namespace)) {
                throw "Unexpected namespace in $($file.FullName). No files have been changed."
            }
            # Relationships formerly resolved other models through the shared root namespace.
            $imports = @()
            $relatedNames = @($classMap.Keys) + @('User', 'Cart')
            foreach ($related in $relatedNames) {
                if ($related -eq $file.BaseName) { continue }
                if ($classMap.ContainsKey($related)) {
                    $relatedClass = $classMap[$related]
                    if ($groups[$related] -eq $record.Group) { continue }
                } else {
                    $relatedClass = "App\Models\$related"
                }
                $reference = '(?<![A-Za-z0-9_\\])' + [regex]::Escape($related) + '\s*::'
                $newReference = '(?<![A-Za-z0-9_\\])new\s+' + [regex]::Escape($related) + '\b'
                $typeReference = ':\s*\??' + [regex]::Escape($related) + '\b'
                $import = "use $relatedClass;"
                if (([regex]::IsMatch($updated, $reference) -or [regex]::IsMatch($updated, $newReference) -or [regex]::IsMatch($updated, $typeReference)) -and !$updated.Contains($import)) {
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
    Write-Host 'Model folders, namespaces and references are already updated.'
    return
}

$backupRoot = Join-Path $projectRoot ('storage\app\model-structure-backups\' + [guid]::NewGuid().ToString('N'))
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

Write-Host "Updated $($changes.Count) files. Model folders and references are ready."
Write-Host "Backup: $backupRoot"
Write-Host 'Next: composer dump-autoload, then php artisan optimize:clear and php artisan route:list.'
