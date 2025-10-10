# Script para iniciar o projeto Laravel 11
# AroliStudio - Laravel 11

Write-Host "=== AroliStudio - Laravel 11 ===" -ForegroundColor Green
Write-Host ""

# Verificar se o Docker está rodando
Write-Host "Verificando Docker..." -ForegroundColor Yellow
docker ps | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Docker não está rodando. Iniciando..." -ForegroundColor Yellow
    docker-compose up -d
    Start-Sleep -Seconds 15
}

Write-Host "✓ Docker está rodando" -ForegroundColor Green

# Verificar se os assets estão compilados
Write-Host ""
Write-Host "Verificando assets..." -ForegroundColor Yellow
$assetsExist = docker-compose exec app test -d public/build
if ($LASTEXITCODE -ne 0) {
    Write-Host "Assets não encontrados. Compilando..." -ForegroundColor Yellow
    docker-compose exec app npm run build
    Write-Host "✓ Assets compilados" -ForegroundColor Green
} else {
    Write-Host "✓ Assets já compilados" -ForegroundColor Green
}

# Verificar migrações
Write-Host ""
Write-Host "Verificando banco de dados..." -ForegroundColor Yellow
docker-compose exec app php artisan migrate:status | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Executando migrações..." -ForegroundColor Yellow
    docker-compose exec app php artisan migrate
    Write-Host "✓ Migrações executadas" -ForegroundColor Green
} else {
    Write-Host "✓ Banco de dados OK" -ForegroundColor Green
}

Write-Host ""
Write-Host "🎉 AroliStudio Laravel 11 está pronto!" -ForegroundColor Green
Write-Host ""
Write-Host "Acesse a aplicação em:" -ForegroundColor Cyan
Write-Host "  http://localhost:8000" -ForegroundColor White
Write-Host ""
Write-Host "Comandos úteis:" -ForegroundColor Yellow
Write-Host "  - Compilar assets: docker-compose exec app npm run build" -ForegroundColor White
Write-Host "  - Ver logs: docker-compose logs -f app" -ForegroundColor White
Write-Host "  - Parar containers: docker-compose down" -ForegroundColor White
Write-Host "  - Reiniciar: docker-compose restart" -ForegroundColor White
