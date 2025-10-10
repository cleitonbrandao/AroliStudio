# Script para atualizar o projeto para Laravel 11
# AroliStudio - Atualização Laravel 11

Write-Host "=== AroliStudio - Atualização para Laravel 11 ===" -ForegroundColor Green
Write-Host ""

# Verificar se estamos no diretório correto
if (-not (Test-Path "composer.json")) {
    Write-Host "✗ Erro: composer.json não encontrado!" -ForegroundColor Red
    Write-Host "Execute este script no diretório raiz do projeto." -ForegroundColor Yellow
    exit 1
}

Write-Host "✓ Diretório do projeto encontrado" -ForegroundColor Green
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
    Start-Sleep -Seconds 10
}

Write-Host ""
Write-Host "Parando containers para atualização..." -ForegroundColor Yellow
docker-compose down

Write-Host ""
Write-Host "Atualizando dependências do Composer..." -ForegroundColor Yellow
Write-Host "Isso pode demorar alguns minutos..." -ForegroundColor Cyan

# Atualizar dependências
composer update --no-interaction

if ($LASTEXITCODE -ne 0) {
    Write-Host ""
    Write-Host "✗ Erro ao atualizar dependências do Composer!" -ForegroundColor Red
    Write-Host "Verifique os logs acima para mais detalhes." -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "✓ Dependências atualizadas com sucesso!" -ForegroundColor Green

# Limpar cache do Composer
Write-Host "Limpando cache do Composer..." -ForegroundColor Yellow
composer clear-cache

# Reconstruir containers
Write-Host ""
Write-Host "Reconstruindo containers com PHP 8.2..." -ForegroundColor Yellow
docker-compose up -d --build

# Aguardar containers ficarem prontos
Write-Host "Aguardando containers ficarem prontos..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Verificar se containers estão rodando
Write-Host "Verificando containers..." -ForegroundColor Yellow
docker ps

# Instalar dependências no container
Write-Host ""
Write-Host "Instalando dependências no container..." -ForegroundColor Yellow
docker-compose exec aroli_app composer install --no-interaction

# Instalar dependências do Node.js
Write-Host "Instalando dependências do Node.js..." -ForegroundColor Yellow
docker-compose exec aroli_app npm install

# Gerar autoloader
Write-Host "Gerando autoloader..." -ForegroundColor Yellow
docker-compose exec aroli_app composer dump-autoload

# Limpar cache do Laravel
Write-Host "Limpando cache do Laravel..." -ForegroundColor Yellow
docker-compose exec aroli_app php artisan cache:clear
docker-compose exec aroli_app php artisan config:clear
docker-compose exec aroli_app php artisan view:clear

# Verificar versão do Laravel
Write-Host ""
Write-Host "Verificando versão do Laravel..." -ForegroundColor Yellow
docker-compose exec aroli_app php artisan --version

# Executar migrações
Write-Host ""
Write-Host "Executando migrações..." -ForegroundColor Yellow
docker-compose exec aroli_app php artisan migrate

Write-Host ""
Write-Host "🎉 Atualização para Laravel 11 concluída com sucesso!" -ForegroundColor Green
Write-Host ""
Write-Host "Próximos passos:" -ForegroundColor Cyan
Write-Host "  1. Compile os assets: .\build-assets.ps1" -ForegroundColor White
Write-Host "  2. Teste a aplicação: http://localhost:8000" -ForegroundColor White
Write-Host "  3. Execute os testes: docker-compose exec aroli_app php artisan test" -ForegroundColor White
Write-Host "  4. Verifique se todas as funcionalidades estão funcionando" -ForegroundColor White
Write-Host ""
Write-Host "Se houver problemas, voce pode voltar com:" -ForegroundColor Yellow
Write-Host "  git checkout v1.0-pre-laravel11" -ForegroundColor White
