# Script para corrigir problemas do Docker
Write-Host "🔧 Corrigindo problemas do Docker..." -ForegroundColor Yellow

# Parar containers
Write-Host "🛑 Parando containers..." -ForegroundColor Yellow
docker-compose down

# Remover containers e volumes
Write-Host "🧹 Removendo containers e volumes..." -ForegroundColor Yellow
docker-compose down -v
docker system prune -f

# Reconstruir containers
Write-Host "🔨 Reconstruindo containers..." -ForegroundColor Yellow
docker-compose up -d --build

# Aguardar containers ficarem prontos
Write-Host "⏳ Aguardando containers ficarem prontos..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Verificar se o vendor existe
Write-Host "📦 Verificando dependências..." -ForegroundColor Yellow
docker-compose exec aroli_app ls -la vendor/

# Se não existir, instalar
Write-Host "📦 Instalando dependências do Composer..." -ForegroundColor Yellow
docker-compose exec aroli_app composer install

# Gerar autoloader
Write-Host "🔄 Gerando autoloader..." -ForegroundColor Yellow
docker-compose exec aroli_app composer dump-autoload

# Verificar se o .env existe
Write-Host "📝 Verificando arquivo .env..." -ForegroundColor Yellow
docker-compose exec aroli_app ls -la .env

# Se não existir, criar
if (!(Test-Path ".env")) {
    Write-Host "📝 Criando arquivo .env..." -ForegroundColor Yellow
    @"
APP_NAME=AroliStudio
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=aroli_studio
DB_USERNAME=aroli_user
DB_PASSWORD=aroli_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
"@ | Out-File -FilePath ".env" -Encoding UTF8
}

# Gerar chave da aplicação
Write-Host "🔑 Gerando chave da aplicação..." -ForegroundColor Yellow
docker-compose exec aroli_app php artisan key:generate

# Executar migrations
Write-Host "📊 Executando migrations..." -ForegroundColor Yellow
docker-compose exec aroli_app php artisan migrate

Write-Host "✅ Problemas corrigidos!" -ForegroundColor Green
Write-Host "🌐 Acesse: http://localhost:8000" -ForegroundColor Cyan
