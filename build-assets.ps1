# Script para compilar assets do projeto
# AroliStudio - Build Assets

Write-Host "=== AroliStudio - Compilando Assets ===" -ForegroundColor Green
Write-Host ""

# Verificar se o Docker está rodando
Write-Host "Verificando Docker..." -ForegroundColor Yellow
docker ps | Out-Null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Docker está rodando" -ForegroundColor Green
} else {
    Write-Host "✗ Docker não está rodando. Iniciando..." -ForegroundColor Yellow
    docker-compose up -d
    Start-Sleep -Seconds 10
}

Write-Host ""
Write-Host "Compilando assets..." -ForegroundColor Yellow
docker-compose exec aroli_app npm run dev

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✓ Assets compilados com sucesso!" -ForegroundColor Green
    Write-Host "A aplicação está pronta para uso." -ForegroundColor Cyan
} else {
    Write-Host ""
    Write-Host "✗ Erro ao compilar assets" -ForegroundColor Red
    Write-Host "Verifique os logs acima para mais detalhes." -ForegroundColor Yellow
}