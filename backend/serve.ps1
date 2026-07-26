# Start Laravel backend with PHP 8.2+ (not XAMPP 8.0)
$ErrorActionPreference = "Stop"
. (Join-Path $PSScriptRoot "..\USE-PHP.ps1")

Set-Location $PSScriptRoot
if (-not (Test-Path "vendor\autoload.php")) {
    Write-Host "Running composer install..." -ForegroundColor Yellow
    if (Get-Command composer -ErrorAction SilentlyContinue) {
        composer install
    } else {
        & $global:VELORA_PHP (Join-Path $PSScriptRoot "composer.phar") install
    }
}

& $global:VELORA_PHP artisan serve
