# Script PowerShell para gerenciar o banco de dados Docker
# Execute este script para iniciar o MySQL

Write-Host "=== AroliStudio - Docker MySQL ===" -ForegroundColor Green
Write-Host ""

# Verificar se o Docker está rodando
try {
    docker version | Out-Null
    Write-Host "✓ Docker está rodando" -ForegroundColor Green
} catch {
    Write-Host "✗ Docker não está rodando. Por favor, inicie o Docker Desktop." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Iniciando o banco de dados MySQL..." -ForegroundColor Yellow

# Iniciar o container
docker-compose up -d

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✓ Banco de dados iniciado com sucesso!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Configurações do banco:" -ForegroundColor Cyan
    Write-Host "  Host: 127.0.0.1" -ForegroundColor White
    Write-Host "  Porta: 3306" -ForegroundColor White
    Write-Host "  Banco: aroli_studio" -ForegroundColor White
    Write-Host "  Usuário: aroli_user" -ForegroundColor White
    Write-Host "  Senha: aroli_password" -ForegroundColor White
    Write-Host ""
    Write-Host "Para parar o banco: docker-compose down" -ForegroundColor Yellow
    Write-Host "Para ver logs: docker-compose logs mysql" -ForegroundColor Yellow
} else {
    Write-Host "✗ Erro ao iniciar o banco de dados" -ForegroundColor Red
}
