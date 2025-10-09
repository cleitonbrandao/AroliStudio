# Script simples para configurar o banco de dados
# AroliStudio - Setup do Banco de Dados

Write-Host "=== AroliStudio - Setup do Banco de Dados ===" -ForegroundColor Green
Write-Host ""

# Verificar se o Docker está rodando
Write-Host "Verificando Docker..." -ForegroundColor Yellow
try {
    docker ps | Out-Null
    Write-Host "✓ Docker está rodando" -ForegroundColor Green
}
catch {
    Write-Host "✗ Docker não está rodando. Iniciando..." -ForegroundColor Yellow
    docker-compose up -d
    Start-Sleep -Seconds 5
}

Write-Host ""
Write-Host "Executando migrações..." -ForegroundColor Yellow
php artisan migrate:fresh

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✓ Banco de dados configurado com sucesso!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Próximos passos:" -ForegroundColor Cyan
    Write-Host "  1. php artisan serve" -ForegroundColor White
    Write-Host "  2. Acesse: http://localhost:8000" -ForegroundColor White
    Write-Host "  3. Registre-se ou faça login" -ForegroundColor White
}
else {
    Write-Host ""
    Write-Host "✗ Erro ao configurar o banco de dados" -ForegroundColor Red
}
