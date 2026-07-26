# VELORA — Windows starter (fixes PHP 8.0 XAMPP issue)
$ErrorActionPreference = "Stop"

. (Join-Path $PSScriptRoot "USE-PHP.ps1")
if ($LASTEXITCODE -eq 1) {
    Read-Host "Press Enter to exit"
    exit 1
}

$php = $global:VELORA_PHP

Write-Host ""
Write-Host "=== VELORA START ===" -ForegroundColor Cyan
& $php -v | Select-Object -First 1

$root = $PSScriptRoot
$backend = Join-Path $root "backend"
$frontend = Join-Path $root "frontend"

Set-Location $backend
Write-Host ""
Write-Host "Setting up database..." -ForegroundColor Green
& $php artisan migrate --force
& $php artisan velora:reset-sport-catalog

Write-Host ""
Write-Host "Starting backend on http://localhost:8000" -ForegroundColor Green
$phpEscaped = $php -replace "'", "''"
$pathEscaped = $env:Path -replace "'", "''"
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$backend'; `$env:Path = '$pathEscaped'; & '$phpEscaped' artisan serve"

Start-Sleep -Seconds 2

Write-Host "Starting frontend on http://localhost:5173" -ForegroundColor Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$frontend'; npm run dev"

Write-Host ""
Write-Host "DONE. Open in browser: http://localhost:5173" -ForegroundColor Cyan
Write-Host "If images are blank, backend is not running — check the backend window for errors." -ForegroundColor Yellow
Read-Host "Press Enter to close this window"
