$ErrorActionPreference = 'Stop'

$projectRoot = (Get-Location).Path
$configRoot = Join-Path $projectRoot 'config'
if (!(Test-Path (Join-Path $projectRoot 'artisan')) -or !(Test-Path $configRoot)) {
    throw 'Run this script from the Laravel project root containing artisan.'
}

$keys = [ordered]@{
    sari_assistant = 'sari.assistant'
    sari_buyer = 'sari.buyer'
    sari_buyer_promotions = 'sari.buyer_promotions'
    sari_chat = 'sari.chat'
    sari_compliance = 'sari.compliance'
}
$configFiles = @{}
foreach ($oldKey in $keys.Keys) {
    $source = Join-Path $configRoot "$oldKey.php"
    $leaf = $keys[$oldKey].Substring('sari.'.Length)
    $target = Join-Path (Join-Path $configRoot 'sari') "$leaf.php"
    $hasSource = Test-Path -LiteralPath $source
    $hasTarget = Test-Path -LiteralPath $target
    if ($hasSource -and $hasTarget) {
        throw "Both source and destination exist for $oldKey. No files have been changed."
    }
    if (!$hasSource -and !$hasTarget) {
        throw "Missing config: $oldKey. No files have been changed."
    }
    $current = $source
    if ($hasTarget) { $current = $target }
    $configFiles[$current] = $target
}

$utf8 = New-Object System.Text.UTF8Encoding($false)
$changes = @()
foreach ($directory in @('app', 'routes', 'tests', 'bootstrap', 'config', 'resources', 'database')) {
    $directoryPath = Join-Path $projectRoot $directory
    if (!(Test-Path $directoryPath)) { continue }
    foreach ($file in Get-ChildItem -LiteralPath $directoryPath -Filter '*.php' -File -Recurse) {
        $original = [System.IO.File]::ReadAllText($file.FullName)
        $updated = $original
        foreach ($oldKey in $keys.Keys) {
            $newKey = $keys[$oldKey]
            $updated = $updated.Replace("'" + $oldKey + '.', "'" + $newKey + '.')
            $updated = $updated.Replace('"' + $oldKey + '.', '"' + $newKey + '.')
            $updated = $updated.Replace("'" + $oldKey + "'", "'" + $newKey + "'")
            $updated = $updated.Replace('"' + $oldKey + '"', '"' + $newKey + '"')
        }
        $target = $file.FullName
        if ($configFiles.ContainsKey($file.FullName)) {
            $target = $configFiles[$file.FullName]
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
    Write-Host 'Custom config folders and references are already updated.'
    return
}

$backupRoot = Join-Path $projectRoot ('storage\app\config-structure-backups\' + [guid]::NewGuid().ToString('N'))
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

Write-Host "Updated $($changes.Count) files. Custom config folders and references are ready."
Write-Host "Backup: $backupRoot"
Write-Host 'Next: composer dump-autoload, then php artisan optimize:clear and php artisan route:list.'
