$ErrorActionPreference = 'Stop'

$projectRoot = (Get-Location).Path
$middlewareRoot = Join-Path $projectRoot 'app\Http\Middleware'
if (!(Test-Path (Join-Path $projectRoot 'artisan')) -or !(Test-Path $middlewareRoot)) {
    throw 'Run this script from the Laravel project root containing artisan.'
}

$groups = [ordered]@{
    EnsureSellerAccountAccessible = 'Seller'
    EnsureSellerNotRestricted = 'Seller'
    HandleSellerSupportChat = 'Seller'
    EnsureLogisticsAuthenticated = 'Logistics'
    CourierOnly = 'Courier'
    EnforcePlatformOperationalRules = 'Platform'
    EnsureSessionAccountAccessible = 'Shared'
}

$classMap = @{}
$middlewareFiles = @{}
foreach ($name in $groups.Keys) {
    $group = $groups[$name]
    $source = Join-Path $middlewareRoot "$name.php"
    $target = Join-Path (Join-Path $middlewareRoot $group) "$name.php"
    $hasSource = Test-Path -LiteralPath $source
    $hasTarget = Test-Path -LiteralPath $target

    if ($hasSource -and $hasTarget) {
        throw "Both source and destination exist for $name. No files have been changed."
    }
    if (!$hasSource -and !$hasTarget) {
        throw "Missing middleware: $name. No files have been changed."
    }

    $current = $source
    if ($hasTarget) { $current = $target }
    $classMap[$name] = "App\Http\Middleware\$group\$name"
    $middlewareFiles[$current] = [PSCustomObject]@{ Group = $group; Target = $target }
}

$names = ($groups.Keys | ForEach-Object { [regex]::Escape($_) }) -join '|'
$referencePattern = [regex]::new('App\\Http\\Middleware\\(?<name>' + $names + ')(?![A-Za-z0-9_\\])')
$rootNamespace = 'namespace App\Http\Middleware;'
$utf8 = New-Object System.Text.UTF8Encoding($false)
$changes = @()

foreach ($directory in @('app', 'routes', 'tests', 'bootstrap', 'config', 'resources')) {
    $directoryPath = Join-Path $projectRoot $directory
    if (!(Test-Path $directoryPath)) { continue }

    foreach ($file in Get-ChildItem -LiteralPath $directoryPath -Filter '*.php' -File -Recurse) {
        $original = [System.IO.File]::ReadAllText($file.FullName)
        $updated = $referencePattern.Replace($original, [System.Text.RegularExpressions.MatchEvaluator] {
            param($match)
            return $classMap[$match.Groups['name'].Value]
        })
        $target = $file.FullName

        if ($middlewareFiles.ContainsKey($file.FullName)) {
            $record = $middlewareFiles[$file.FullName]
            $namespace = "namespace App\Http\Middleware\$($record.Group);"
            if ($updated.Contains($rootNamespace)) {
                $updated = $updated.Replace($rootNamespace, $namespace)
            } elseif (!$updated.Contains($namespace)) {
                throw "Unexpected namespace in $($file.FullName). No files have been changed."
            }
            $target = $record.Target
        }

        if ($updated -cne $original -or $target -ne $file.FullName) {
            $changes += [PSCustomObject]@{
                Source = $file.FullName
                Target = $target
                RelativePath = $file.FullName.Substring($projectRoot.TrimEnd('\').Length + 1)
                Content = $updated
            }
        }
    }
}

if ($changes.Count -eq 0) {
    Write-Host 'Middleware folders, namespaces and references are already updated.'
    return
}

$backupRoot = Join-Path $projectRoot ('storage\app\middleware-structure-backups\' + [guid]::NewGuid().ToString('N'))
foreach ($change in $changes) {
    $backupPath = Join-Path $backupRoot $change.RelativePath
    New-Item -ItemType Directory -Path (Split-Path $backupPath -Parent) -Force | Out-Null
    Copy-Item -LiteralPath $change.Source -Destination $backupPath
}

try {
    foreach ($change in $changes) {
        New-Item -ItemType Directory -Path (Split-Path $change.Target -Parent) -Force | Out-Null
        [System.IO.File]::WriteAllText($change.Target, $change.Content, $utf8)
    }
    foreach ($change in $changes) {
        if ($change.Source -ne $change.Target) {
            Remove-Item -LiteralPath $change.Source
        }
    }
} catch {
    foreach ($change in $changes) {
        Copy-Item -LiteralPath (Join-Path $backupRoot $change.RelativePath) -Destination $change.Source -Force
        if ($change.Source -ne $change.Target -and (Test-Path -LiteralPath $change.Target)) {
            Remove-Item -LiteralPath $change.Target
        }
    }
    throw 'Update failed. Original files and locations were restored from the backup.'
}

Write-Host "Updated $($changes.Count) files. Middleware folders and references are ready."
Write-Host "Backup: $backupRoot"
Write-Host 'Next: composer dump-autoload, then php artisan optimize:clear and php artisan route:list.'
