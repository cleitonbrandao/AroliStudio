# Script de desenvolvimento para AroliStudio
param(
    [string]$Command = "help"
)

switch ($Command) {
    "start" {
        Write-Host "🚀 Iniciando ambiente de desenvolvimento..." -ForegroundColor Green
        docker-compose up -d
        Write-Host "✅ Ambiente iniciado! Acesse: http://localhost:8000" -ForegroundColor Cyan
    }
    
    "stop" {
        Write-Host "🛑 Parando ambiente de desenvolvimento..." -ForegroundColor Yellow
        docker-compose down
        Write-Host "✅ Ambiente parado!" -ForegroundColor Green
    }
    
    "restart" {
        Write-Host "🔄 Reiniciando ambiente de desenvolvimento..." -ForegroundColor Yellow
        docker-compose down
        docker-compose up -d
        Write-Host "✅ Ambiente reiniciado!" -ForegroundColor Green
    }
    
    "build" {
        Write-Host "🔨 Construindo containers..." -ForegroundColor Yellow
        docker-compose up -d --build
        Write-Host "✅ Containers construídos!" -ForegroundColor Green
    }
    
    "logs" {
        Write-Host "📋 Exibindo logs..." -ForegroundColor Yellow
        docker-compose logs -f
    }
    
    "migrate" {
        Write-Host "📊 Executando migrations..." -ForegroundColor Yellow
        docker-compose exec app php artisan migrate
    }
    
    "seed" {
        Write-Host "🌱 Executando seeders..." -ForegroundColor Yellow
        docker-compose exec app php artisan db:seed
    }
    
    "fresh" {
        Write-Host "🔄 Recriando banco de dados..." -ForegroundColor Yellow
        docker-compose exec app php artisan migrate:fresh --seed
    }
    
    "artisan" {
        $artisanCommand = $args -join " "
        Write-Host "⚡ Executando: php artisan $artisanCommand" -ForegroundColor Yellow
        docker-compose exec app php artisan $artisanCommand
    }
    
    "composer" {
        $composerCommand = $args -join " "
        Write-Host "📦 Executando: composer $composerCommand" -ForegroundColor Yellow
        docker-compose exec app composer $composerCommand
    }
    
    "npm" {
        $npmCommand = $args -join " "
        Write-Host "📦 Executando: npm $npmCommand" -ForegroundColor Yellow
        docker-compose exec app npm $npmCommand
    }
    
    "shell" {
        Write-Host "🐚 Abrindo shell do container..." -ForegroundColor Yellow
        docker-compose exec app bash
    }
    
    "mysql" {
        Write-Host "🗄️ Conectando ao MySQL..." -ForegroundColor Yellow
        docker-compose exec mysql mysql -u aroli_user -p aroli_studio
    }
    
    "redis" {
        Write-Host "🔴 Conectando ao Redis..." -ForegroundColor Yellow
        docker-compose exec redis redis-cli
    }
    
    "status" {
        Write-Host "📊 Status dos containers:" -ForegroundColor Yellow
        docker-compose ps
    }
    
    "clean" {
        Write-Host "🧹 Limpando containers e volumes..." -ForegroundColor Yellow
        docker-compose down -v
        docker system prune -f
        Write-Host "✅ Limpeza concluída!" -ForegroundColor Green
    }
    
    default {
        Write-Host "🛠️  AroliStudio - Scripts de Desenvolvimento" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Comandos disponíveis:" -ForegroundColor White
        Write-Host "  ./dev.ps1 start     - Iniciar ambiente" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 stop      - Parar ambiente" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 restart   - Reiniciar ambiente" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 build     - Construir containers" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 logs      - Exibir logs" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 migrate   - Executar migrations" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 seed      - Executar seeders" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 fresh     - Recriar banco de dados" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 artisan   - Executar comando artisan" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 composer  - Executar comando composer" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 npm       - Executar comando npm" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 shell     - Abrir shell do container" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 mysql     - Conectar ao MySQL" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 redis     - Conectar ao Redis" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 status    - Status dos containers" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 clean     - Limpar containers e volumes" -ForegroundColor Gray
        Write-Host ""
        Write-Host "Exemplos:" -ForegroundColor White
        Write-Host "  ./dev.ps1 artisan make:model Product" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 composer install" -ForegroundColor Gray
        Write-Host "  ./dev.ps1 npm run dev" -ForegroundColor Gray
    }
}
