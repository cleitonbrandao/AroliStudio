# Plano de Atualização - AroliStudio

## 🎯 Objetivo
Atualizar o projeto AroliStudio do Laravel 10 para Laravel 11 (versão atual estável) e configurar Docker com FrankenPHP.

## 📋 Fase 1: Atualização para Laravel 11

### Pré-requisitos
- ✅ PHP 8.2+ (atual: 8.1 - precisa atualizar)
- ✅ Composer 2.5+
- ✅ Node.js 18+

### Passos da Atualização

#### 1. Backup do Projeto
```bash
git add .
git commit -m "Backup antes da atualização Laravel 11"
git tag -a v1.0-pre-upgrade -m "Versão antes da atualização"
```

#### 2. Atualizar composer.json
```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0",
        "laravel/jetstream": "^5.0",
        "laravel/sanctum": "^4.0",
        "livewire/livewire": "^3.5",
        "wire-elements/modal": "^2.0"
    },
    "require-dev": {
        "laravel/pint": "^1.13",
        "laravel/sail": "^1.26",
        "phpunit/phpunit": "^11.0"
    }
}
```

#### 3. Atualizar Dependências
```bash
composer update
```

#### 4. Verificar Compatibilidade
- Jetstream 5.x (compatível com Laravel 11)
- Livewire 3.x (compatível)
- Sanctum 4.x (compatível)

## 📋 Fase 2: Configuração Docker com FrankenPHP

### Dockerfile para Desenvolvimento
```dockerfile
FROM php:8.2-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    zip unzip libzip-dev default-mysql-client \
    libfreetype6-dev libjpeg62-turbo-dev libgd-dev \
    libicu-dev

# Instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Instalar Redis
RUN pecl install redis && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html
```

### Dockerfile para Produção com FrankenPHP
```dockerfile
FROM dunglas/frankenphp:1-php8.2

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    zip unzip libzip-dev default-mysql-client \
    libfreetype6-dev libjpeg62-turbo-dev libgd-dev \
    libicu-dev

# Instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Instalar Redis
RUN pecl install redis && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html

# Copiar aplicação
COPY . .

# Instalar dependências
RUN composer install --optimize-autoloader --no-dev \
    && npm ci --only=production \
    && npm run build

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
```

## 📋 Fase 3: Configuração do Docker Compose

### docker-compose.yml Atualizado
```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.dev
    container_name: aroli_app
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - .:/var/www/html
      - ./storage:/var/www/html/storage
      - ./bootstrap/cache:/var/www/html/bootstrap/cache
    ports:
      - "8000:80"
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
      - DB_HOST=mysql
      - DB_DATABASE=aroli_studio
      - DB_USERNAME=aroli_user
      - DB_PASSWORD=aroli_password
      - CACHE_DRIVER=redis
      - SESSION_DRIVER=redis
      - REDIS_HOST=redis
    depends_on:
      - mysql
      - redis
    networks:
      - aroli_network

  mysql:
    image: mysql:8.0
    container_name: aroli_mysql
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: aroli_studio
      MYSQL_USER: aroli_user
      MYSQL_PASSWORD: aroli_password
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
      - ./docker/mysql/init:/docker-entrypoint-initdb.d
    networks:
      - aroli_network
    command: --default-authentication-plugin=mysql_native_password

  redis:
    image: redis:7-alpine
    container_name: aroli_redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    networks:
      - aroli_network

volumes:
  mysql_data:
    driver: local

networks:
  aroli_network:
    driver: bridge
```

## 📋 Fase 4: Configuração do Laravel Octane

### Instalação do Octane
```bash
composer require laravel/octane
php artisan octane:install --server=frankenphp
```

### Configuração do Octane
```php
// config/octane.php
return [
    'server' => env('OCTANE_SERVER', 'frankenphp'),
    'host' => env('OCTANE_HOST', '0.0.0.0'),
    'port' => env('OCTANE_PORT', 8000),
    'admin_host' => env('OCTANE_ADMIN_HOST', '127.0.0.1'),
    'admin_port' => env('OCTANE_ADMIN_PORT', 2019),
    'workers' => env('OCTANE_WORKERS', 4),
    'max_requests' => env('OCTANE_MAX_REQUESTS', 1000),
];
```

## 🚨 Riscos e Considerações

### Riscos Identificados
1. **Breaking Changes**: Laravel 11 pode ter mudanças que quebrem funcionalidades
2. **Dependências**: Algumas dependências podem não ser compatíveis
3. **Migrações**: Pode ser necessário ajustar migrações existentes
4. **Testes**: Todos os testes precisam ser atualizados

### Mitigações
1. **Backup completo** antes da atualização
2. **Testes extensivos** após cada fase
3. **Atualização gradual** (10 → 11 → 12 quando disponível)
4. **Ambiente de desenvolvimento** separado para testes

## 📅 Cronograma Estimado

- **Fase 1**: 2-3 dias (atualização Laravel 11)
- **Fase 2**: 1-2 dias (configuração Docker)
- **Fase 3**: 1 dia (Docker Compose)
- **Fase 4**: 1-2 dias (Octane + FrankenPHP)
- **Testes**: 2-3 dias (testes completos)

**Total**: 7-11 dias

## ✅ Conclusão

A atualização é **viável**, mas recomendo:
1. **Começar com Laravel 11** (versão atual estável)
2. **Aguardar Laravel 12** ser lançado oficialmente
3. **Implementar Docker com FrankenPHP** gradualmente
4. **Manter backup** e ambiente de teste

Quer que eu implemente a Fase 1 (atualização para Laravel 11) primeiro?
