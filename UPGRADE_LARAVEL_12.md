# Guia de Atualização Laravel 12 + Livewire 3.5

## Passos para atualizar o projeto

### 1. Atualizar dependências do Composer

```bash
cd src
composer update
```

Se houver conflitos, tente:
```bash
composer update --with-all-dependencies
```

### 2. Limpar todos os caches

```bash
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 3. Publicar assets do Laravel 12

```bash
php artisan vendor:publish --tag=laravel-assets --ansi --force
```

### 4. Publicar assets do Livewire (se necessário)

```bash
php artisan livewire:publish --assets
```

### 5. Atualizar dependências Node.js

```bash
npm install
npm run build
```

### 6. Testar a aplicação

```bash
php artisan test
php artisan serve
```

## Mudanças importantes

### Kernel.php → bootstrap/app.php

O Laravel 12 não usa mais o arquivo `app/Http/Kernel.php`. Toda a configuração de middlewares foi migrada para `bootstrap/app.php`.

**O que foi migrado:**
- Middlewares web: `SetActiveCompany`, `SetLocale`
- Aliases de middlewares customizados: `subscription`, `company.permission`, `user.has.company`

### Estrutura de middlewares no Laravel 12

No novo `bootstrap/app.php`, os middlewares são configurados assim:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SetActiveCompany::class,
        \App\Http\Middleware\SetLocale::class,
    ]);

    $middleware->alias([
        'subscription' => \App\Http\Middleware\CheckSubscriptionLimits::class,
        'company.permission' => \App\Http\Middleware\CheckCompanyPermission::class,
        'user.has.company' => \App\Http\Middleware\EnsureUserHasCompany::class,
    ]);
})
```

## Possíveis problemas e soluções

### Erro: Class 'App\Http\Middleware\SetLocale' not found

Se o middleware `SetLocale` ainda não existir, crie-o com:

```bash
php artisan make:middleware SetLocale
```

Ou remova a referência em `bootstrap/app.php` se não for necessário.

### Erro de dependências conflitantes

Se houver problemas com pacotes incompatíveis, verifique:
- `laravel/jetstream` deve estar em `^5.1`
- `laravel/tinker` deve estar em `^3.0`
- `livewire/livewire` deve estar em `^3.5`

### Banco de dados

Execute as migrações pendentes:

```bash
php artisan migrate
```

## Após a atualização

1. Verifique se todas as rotas funcionam
2. Teste componentes Livewire
3. Verifique logs em `storage/logs/laravel.log`
4. Teste autenticação e autorização
5. Verifique se os middlewares customizados estão funcionando

## Rollback (se necessário)

Se algo der errado, você pode reverter:

```bash
git checkout composer.json bootstrap/app.php
composer install
```

## Suporte

- Documentação Laravel 12: https://laravel.com/docs/12.x
- Documentação Livewire 3: https://livewire.laravel.com/docs/3.x
- Guia de atualização: https://laravel.com/docs/12.x/upgrade
