# VELORA — force PHP 8.2+ (skip XAMPP 8.0)
$ErrorActionPreference = "Stop"

# Remove XAMPP PHP 8.0 from PATH for this session
$env:Path = ($env:Path -split ';' | Where-Object { $_ -and $_ -notmatch 'xampp\\php' }) -join ';'

$candidates = @(
    "$env:LOCALAPPDATA\Programs\PHP\current",
    "$env:LOCALAPPDATA\Programs\PHP\php-8.5.0",
    "$env:LOCALAPPDATA\Programs\PHP\php-8.5",
    "$env:LOCALAPPDATA\Programs\PHP\php-8.3.0",
    "$env:LOCALAPPDATA\Programs\PHP\php-8.2.0",
    "C:\php85",
    "C:\php",
    "C:\php-8.5",
    "C:\tools\php85",
    "C:\Program Files\PHP"
)

$phpExe = $null
foreach ($dir in $candidates) {
    $exe = Join-Path $dir "php.exe"
    if (-not (Test-Path $exe)) { continue }
    $ver = & $exe -r "echo PHP_VERSION;" 2>$null
    if ($ver -match '^8\.[2-9]') {
        $phpExe = $exe
        $env:Path = "$dir;$env:Path"
        break
    }
}

if (-not $phpExe) {
    Write-Host ""
    Write-Host "Could not find PHP 8.2+ on this PC." -ForegroundColor Red
    Write-Host ""
    Write-Host "Searched:" -ForegroundColor Yellow
    $candidates | ForEach-Object { Write-Host "  $_" }
    Write-Host ""
    Write-Host "Run this to find your PHP 8.5 folder:" -ForegroundColor Cyan
    Write-Host '  Get-ChildItem -Path C:\,$env:LOCALAPPDATA\Programs\PHP -Filter php.exe -Recurse -ErrorAction SilentlyContinue | ForEach-Object { $_.FullName; & $_.FullName -v | Select-Object -First 1; "" }'
    Write-Host ""
    Write-Host "Then edit USE-PHP.ps1 and add your folder to `$candidates, or run:" -ForegroundColor Cyan
    Write-Host '  $env:Path = "C:\YOUR\PHP85\FOLDER;" + $env:Path'
    exit 1
}

Write-Host "Using PHP: $phpExe" -ForegroundColor Green
& $phpExe -v | Select-Object -First 1
$global:VELORA_PHP = $phpExe
