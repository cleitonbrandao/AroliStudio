# 🐛 Diagnóstico: Locale não Persiste na Sessão

## 🔍 Problemas Identificados

### 1. **Locale Padrão Incorreto** ⚠️
**Arquivo:** `config/app.php`
**Problema:** 
```php
'locale' => 'en',  // ❌ Define inglês como padrão
```

**Impacto:**
- Quando não há locale na sessão → volta para `'en'`
- `'en'` mapeia para USD no `config/currency.php`
- Por isso sempre mostra USD ao recarregar

**Solução:** Mudar para português brasileiro:
```php
'locale' => 'pt_BR',  // ✅ Português como padrão
```

---

### 2. **Possível Problema com Sessão Database**
**Arquivo:** `.env`
**Configuração Atual:**
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

**Possíveis Causas:**
- ✅ Tabela `sessions` existe
- ⚠️ Mas pode haver problemas de persistência
- ⚠️ Sessão pode estar expirando rapidamente
- ⚠️ CSRF token pode estar invalidando a sessão

---

### 3. **Middleware Pode Não Estar Executando Corretamente**
**Arquivo:** `bootstrap/app.php`
**Configuração Atual:**
```php
$middleware->web(append: [
    \App\Http\Middleware\SetActiveCompany::class,
    \App\Http\Middleware\SetLocale::class,  // Executa por último
]);
```

**Possível Problema:**
- Se `SetActiveCompany` falhar, `SetLocale` não executa
- Ordem pode estar causando problemas

---

### 4. **Falta de Feedback Visual**
**Problema:** 
- Usuário clica no locale
- Form submete
- Mas não há feedback se funcionou
- Se falhar silenciosamente, usuário não sabe

---

## 🔧 Soluções Propostas

### Solução 1: Alterar Locale Padrão (OBRIGATÓRIO)
```php
// config/app.php
'locale' => 'pt_BR',
'fallback_locale' => 'pt_BR',
```

### Solução 2: Adicionar Logging ao LocaleController
```php
public function change(Request $request)
{
    $locale = $request->input('locale', config('app.locale'));
    
    \Log::info('Mudando locale', [
        'from' => app()->getLocale(),
        'to' => $locale,
        'session_id' => session()->getId(),
    ]);
    
    if (!in_array($locale, self::VALID_LOCALES)) {
        \Log::warning('Locale inválido', ['locale' => $locale]);
        return redirect()->back()->with('error', __('app.invalid_locale'));
    }
    
    Session::put('locale', $locale);
    Session::save(); // 👈 Força salvar imediatamente
    
    \Log::info('Locale salvo na sessão', [
        'locale' => Session::get('locale'),
        'session_data' => Session::all(),
    ]);
    
    $currency = config("currency.locale_currency_map.{$locale}", config('currency.default'));
    
    return redirect()->back()->with([
        'success' => __('app.locale_changed'),
        'locale' => $locale,
        'currency' => $currency,
    ]);
}
```

### Solução 3: Verificar SetLocale Middleware
```php
public function handle(Request $request, Closure $next): Response
{
    // Obtém o locale da sessão ou usa o padrão
    $locale = Session::get('locale', config('app.locale'));
    
    \Log::debug('SetLocale middleware', [
        'session_locale' => Session::get('locale'),
        'config_locale' => config('app.locale'),
        'final_locale' => $locale,
    ]);
    
    // Define o locale da aplicação
    App::setLocale($locale);
    
    // Define a moeda baseada no locale
    $currency = config("currency.locale_currency_map.{$locale}", config('currency.default'));
    Session::put('currency', $currency);
    
    // Disponibiliza a moeda para todas as views
    view()->share('currentCurrency', $currency);
    view()->share('currentLocale', $locale);
    
    return $next($request);
}
```

### Solução 4: Adicionar Feedback Visual
```blade
<!-- navigation-menu.blade.php - Adicionar antes de </div> do header -->
@if (session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         class="fixed top-4 right-4 z-50 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg shadow-lg"
    >
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         class="fixed top-4 right-4 z-50 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg shadow-lg"
    >
        {{ session('error') }}
    </div>
@endif
```

### Solução 5: Testar com Driver File
Se database continuar com problemas, mudar temporariamente:
```env
SESSION_DRIVER=file
```

### Solução 6: Verificar Permissões da Tabela Sessions
```bash
php artisan tinker --execute="
    \$session = \Illuminate\Support\Facades\DB::table('sessions')->first();
    dd(\$session);
"
```

---

## 🧪 Testes para Diagnosticar

### Teste 1: Verificar se Sessão Persiste
```bash
php artisan tinker
```
```php
use Illuminate\Support\Facades\Session;

// Salvar
Session::put('teste_locale', 'pt_BR');
Session::save();

// Verificar
echo Session::get('teste_locale'); // Deve retornar 'pt_BR'

// Verificar na tabela
DB::table('sessions')->latest('last_activity')->first();
```

### Teste 2: Verificar Middleware
Adicionar `dd()` temporário no SetLocale:
```php
public function handle(Request $request, Closure $next): Response
{
    $locale = Session::get('locale', config('app.locale'));
    
    dd([
        'session_locale' => Session::get('locale'),
        'config_locale' => config('app.locale'),
        'final_locale' => $locale,
        'all_session' => Session::all(),
    ]);
    
    // ...
}
```

### Teste 3: Verificar LocaleController
```bash
curl -X POST http://localhost:8000/locale/change \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "locale=pt_BR&_token=SEU_CSRF_TOKEN"
```

---

## 📋 Checklist de Resolução

- [ ] 1. Alterar `config/app.php` para `'locale' => 'pt_BR'`
- [ ] 2. Limpar cache de configuração: `php artisan config:clear`
- [ ] 3. Adicionar logging ao LocaleController
- [ ] 4. Adicionar `Session::save()` após `Session::put()`
- [ ] 5. Verificar logs: `tail -f storage/logs/laravel.log`
- [ ] 6. Testar mudança de locale
- [ ] 7. Recarregar página e verificar persistência
- [ ] 8. Navegar para outra rota e verificar
- [ ] 9. Se falhar, mudar para `SESSION_DRIVER=file`
- [ ] 10. Adicionar feedback visual de sucesso/erro

---

## 🎯 Resultado Esperado

Após correções:
1. ✅ Usuário seleciona "Português"
2. ✅ Form submete para `locale.change`
3. ✅ Controller salva `'pt_BR'` na sessão
4. ✅ Redirect com mensagem de sucesso
5. ✅ SetLocale middleware lê `'pt_BR'` da sessão
6. ✅ App::setLocale('pt_BR')
7. ✅ Moeda mapeada para BRL
8. ✅ Interface mostra "R$ 1.234,56"
9. ✅ Ao recarregar: mantém pt_BR
10. ✅ Ao navegar: mantém pt_BR

---

## 🔥 Solução Rápida (Quick Fix)

**1. Alterar locale padrão:**
```bash
# Em config/app.php, linha 86
'locale' => 'pt_BR',
```

**2. Limpar cache:**
```bash
php artisan config:clear
php artisan cache:clear
```

**3. Forçar salvar sessão no controller:**
```php
Session::put('locale', $locale);
Session::save(); // 👈 ADICIONAR ESTA LINHA
```

**4. Testar:**
- Selecionar idioma
- Recarregar página (F5)
- Verificar se mantém

Se ainda falhar:
```env
SESSION_DRIVER=file  # Mudar de database para file
```
