param([string]$ProjectRoot = (Get-Location).Path)
$ErrorActionPreference = 'Stop'
$ProjectRoot = [IO.Path]::GetFullPath($ProjectRoot)
$layoutPath = Join-Path $ProjectRoot 'resources\views\layouts\buyer.blade.php'
$cssPath = Join-Path $ProjectRoot 'public\css\buyer\home-theme-fixes.css'
$sourceCss = Join-Path $PSScriptRoot 'public\css\buyer\home-theme-fixes.css'
if (!(Test-Path (Join-Path $ProjectRoot 'artisan')) -or !(Test-Path $layoutPath)) {
    throw 'Run this installer from your Laravel project folder, or pass -ProjectRoot with its full path.'
}
if (!(Test-Path $sourceCss)) { throw 'The patch CSS is missing. Extract the entire ZIP before running INSTALL.ps1.' }
$layout = [IO.File]::ReadAllText($layoutPath)
if ($layout -notmatch '</head>') { throw 'Buyer layout has no closing head tag. No files were changed.' }
$stamp = Get-Date -Format 'yyyyMMdd-HHmmss-fff'
$backup = Join-Path $ProjectRoot ('storage\app\sari-ui-backups\buyer-theme-' + $stamp)
New-Item -ItemType Directory -Path $backup -Force | Out-Null
Copy-Item -LiteralPath $layoutPath -Destination (Join-Path $backup 'buyer.blade.php')
if (Test-Path $cssPath) { Copy-Item -LiteralPath $cssPath -Destination (Join-Path $backup 'home-theme-fixes.css') }
$link = '    <link id="sariBuyerHomeThemeFixes" rel="stylesheet" href="{{ asset(''css/buyer/home-theme-fixes.css'') }}?v=20261114-2">'
$pattern = '(?m)^\s*<link\b[^>]*\bid="sariBuyerHomeThemeFixes"[^>]*>\s*\r?\n?'
$layout = [regex]::Replace($layout, $pattern, '')
$headEnd = $layout.IndexOf('</head>')
$layout = $layout.Insert($headEnd, $link + [Environment]::NewLine)
New-Item -ItemType Directory -Path (Split-Path $cssPath) -Force | Out-Null
Copy-Item -LiteralPath $sourceCss -Destination $cssPath -Force
[IO.File]::WriteAllText($layoutPath, $layout, (New-Object Text.UTF8Encoding($false)))
Write-Host 'Installed Buyer dark cards with white image areas, white category/sidebar text, original logo icon, and seamless warm hero.'
Write-Host ('Original files backed up at: ' + $backup)
Write-Host 'Run: php artisan view:clear'
Write-Host 'Then refresh with Ctrl+F5 and check light/dark modes.'
