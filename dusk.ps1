param([string]$Target = "")

$Port   = 9000
$AppDir = $PSScriptRoot

Write-Host ""
Write-Host "[dusk] Starting dev server on port $Port ..." -ForegroundColor Cyan

$job = Start-Job -ScriptBlock {
    param($dir, $port)
    Set-Location $dir
    php artisan serve --port=$port --env=dusk.local 2>&1
} -ArgumentList $AppDir, $Port

$ready = $false
for ($i = 0; $i -lt 20; $i++) {
    Start-Sleep -Milliseconds 500
    try {
        Invoke-WebRequest -Uri "http://localhost:$Port" -UseBasicParsing -TimeoutSec 2 -ErrorAction Stop | Out-Null
        $ready = $true; break
    } catch { }
}

if ($ready) {
    Write-Host "[dusk] Server ready at http://localhost:$Port" -ForegroundColor Green
} else {
    Write-Host "[dusk] Server may not be ready, running tests anyway..." -ForegroundColor Yellow
}

Set-Location $AppDir
Write-Host "[dusk] Running browser tests..." -ForegroundColor Cyan
Write-Host ""

if ($Target) {
    php artisan dusk $Target
} else {
    php artisan dusk
}

$exitCode = $LASTEXITCODE

Write-Host ""
Write-Host "[dusk] Stopping server..." -ForegroundColor Cyan
Stop-Job  $job -ErrorAction SilentlyContinue
Remove-Job $job -ErrorAction SilentlyContinue

exit $exitCode