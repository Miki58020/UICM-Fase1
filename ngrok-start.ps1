# Inicia ngrok en segundo plano
$ngrokCmd = Get-Command ngrok -ErrorAction SilentlyContinue
$ngrokPath = if ($ngrokCmd) { $ngrokCmd.Source } else { $null }
if (-not $ngrokPath) {
    $ngrokPath = "$env:LOCALAPPDATA\Microsoft\WinGet\Packages\Ngrok.Ngrok_Microsoft.Winget.Source_8wekyb3d8bbwe\ngrok.exe"
}
$ngrokProcess = Start-Process -FilePath $ngrokPath `
    -ArgumentList "http --request-header-add `"ngrok-skip-browser-warning:true`" 8000" `
    -PassThru -WindowStyle Hidden

Write-Host "Esperando que ngrok inicie..."
Start-Sleep -Seconds 5

# Obtiene la URL publica desde la API local de ngrok
try {
    $tunnels = Invoke-RestMethod -Uri "http://localhost:4040/api/tunnels"
    $ngrokUrl = ($tunnels.tunnels | Where-Object { $_.proto -eq "https" } | Select-Object -First 1).public_url
} catch {
    Write-Host "Error: No se pudo obtener la URL de ngrok. Asegurate de que ngrok este instalado."
    Stop-Process -Id $ngrokProcess.Id -Force -ErrorAction SilentlyContinue
    exit 1
}

if (-not $ngrokUrl) {
    Write-Host "Error: ngrok no devolvio una URL HTTPS."
    Stop-Process -Id $ngrokProcess.Id -Force -ErrorAction SilentlyContinue
    exit 1
}

Write-Host "URL de ngrok: $ngrokUrl"

# Actualiza APP_URL y APP_ENV en el .env
$env = Get-Content .env
$env = $env -replace "^APP_URL=.*", "APP_URL=$ngrokUrl"
$env = $env -replace "^ASSET_URL=.*", "ASSET_URL=$ngrokUrl"
$env = $env -replace "^APP_ENV=.*", "APP_ENV=staging"
$env | Set-Content .env

Write-Host ""
Write-Host "=================================="
Write-Host " Comparte esta URL:"
Write-Host " $ngrokUrl"
Write-Host "=================================="
Write-Host ""
Write-Host "Presiona Ctrl+C para detener ngrok y restaurar APP_URL=http://localhost"

try {
    Wait-Process -Id $ngrokProcess.Id
} finally {
    # Restaura el .env al salir
    Write-Host ""
    Write-Host "Restaurando .env..."
    $env = Get-Content .env
    $env = $env -replace "^APP_URL=.*", "APP_URL=http://localhost"
    $env = $env -replace "^ASSET_URL=.*", "ASSET_URL=http://localhost"
    $env = $env -replace "^APP_ENV=.*", "APP_ENV=local"
    $env | Set-Content .env
    Stop-Process -Id $ngrokProcess.Id -Force -ErrorAction SilentlyContinue
    Write-Host "Listo."
}
