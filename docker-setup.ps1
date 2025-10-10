# Script para configurar Docker completo
Write-Host "=== AroliStudio - Docker Setup ===" -ForegroundColor Green
Write-Host ""

# Parar containers existentes
Write-Host "Parando containers existentes..." -ForegroundColor Yellow
docker-compose down

# Remover volumes antigos
Write-Host "Removendo volumes antigos..." -ForegroundColor Yellow
docker-compose down -v

# Limpar sistema Docker
Write-Host "Limpando sistema Docker..." -ForegroundColor Yellow
docker system prune -f

# Reconstruir e iniciar containers
Write-Host "Reconstruindo containers..." -ForegroundColor Yellow
docker-compose up -d --build

# Aguardar containers ficarem prontos
Write-Host "Aguardando containers ficarem prontos..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Verificar se containers estão rodando
Write-Host "Verificando containers..." -ForegroundColor Yellow
docker ps

# Instalar dependências do Composer
Write-Host "Instalando dependencias do Composer..." -ForegroundColor Yellow
docker-compose exec aroli_app composer install

# Gerar autoloader
Write-Host "Gerando autoloader..." -ForegroundColor Yellow
docker-compose exec aroli_app composer dump-autoload

# Gerar chave da aplicação
Write-Host "Gerando chave da aplicacao..." -ForegroundColor Yellow
docker-compose exec aroli_app php artisan key:generate

# Executar migrações
Write-Host "Executando migracoes..." -ForegroundColor Yellow
docker-compose exec aroli_app php artisan migrate

Write-Host ""
Write-Host "Setup concluido com sucesso!" -ForegroundColor Green
Write-Host "Acesse: http://localhost:8000" -ForegroundColor Cyan
