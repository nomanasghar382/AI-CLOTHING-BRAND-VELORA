# VELORA — Windows starter (fixes PHP 8.0 XAMPP issue)
$ErrorActionPreference = "Stop"

$phpPaths = @(
    "$env:LOCALAPPDATA\Programs\PHP\current",
    "$env:LOCALAPPDATA\Programs\PHP\php-8.5.0",
    "$env:LOCALAPPDATA\Programs\PHP\php-8.3.0",
    "$env:LOCALAPPDATA\Programs\PHP\php-8.2.0"
)

foreach ($path in $phpPaths) {
    if (Test-Path "$path\php.exe") {
        $env:Path = "$path;" + $env:Path
        break
    }
}

Write-Host ""
Write-Host "=== VELORA START ===" -ForegroundColor Cyan
$version = & php -v 2>&1 | Select-Object -First 1
Write-Host $version

if ($version -notmatch "PHP 8\.[2-9]") {
    Write-Host ""
    Write-Host "ERROR: You need PHP 8.2 or newer." -ForegroundColor Red
    Write-Host "XAMPP PHP 8.0 will NOT work. Install PHP 8.2+ from https://windows.php.net/download/" -ForegroundColor Yellow
    Write-Host "Then add it to PATH before XAMPP, or edit this script with your PHP folder." -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

$root = $PSScriptRoot
$backend = Join-Path $root "backend"
$frontend = Join-Path $root "frontend"

Set-Location $backend
Write-Host ""
Write-Host "Setting up database..." -ForegroundColor Green
php artisan migrate --force
php artisan velora:reset-sport-catalog

Write-Host ""
Write-Host "Starting backend on http://localhost:8000" -ForegroundColor Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$backend'; `$env:Path = '$($env:Path -replace "'", "''")'; php artisan serve"

Start-Sleep -Seconds 2

Write-Host "Starting frontend on http://localhost:5173" -ForegroundColor Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$frontend'; npm run dev"

Write-Host ""
Write-Host "DONE. Open in browser: http://localhost:5173" -ForegroundColor Cyan
Write-Host "If images are blank, backend is not running — check the backend window for errors." -ForegroundColor Yellow
Read-Host "Press Enter to close this window"
