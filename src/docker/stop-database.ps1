# Script PowerShell para parar o banco de dados Docker

Write-Host "=== Parando o banco de dados MySQL ===" -ForegroundColor Yellow
Write-Host ""

# Parar o container
docker-compose down

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Banco de dados parado com sucesso!" -ForegroundColor Green
} else {
    Write-Host "✗ Erro ao parar o banco de dados" -ForegroundColor Red
}
