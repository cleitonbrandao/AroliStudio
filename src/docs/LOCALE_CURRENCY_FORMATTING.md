# Sistema de Locale e Formatação Monetária

## 🌍 Como Funciona

O sistema detecta automaticamente o **locale** (idioma/região) escolhido pelo usuário e formata os valores monetários de acordo com as convenções daquela região.

---

## 🔄 Fluxo Completo

```
1. Usuário escolhe idioma (pt_BR, en, es, etc.)
   ↓
2. SetLocale Middleware detecta e salva na sessão
   ↓
3. Define locale da aplicação: App::setLocale($locale)
   ↓
4. Define moeda baseada no locale (via config/currency.php)
   ↓
5. MoneyWrapper usa App::getLocale() automaticamente
   ↓
6. brick/money formata com base no locale (ext-intl)
   ↓
7. Resultado: formato correto para cada região
```

---

## 📋 Configuração Atual

### `config/currency.php`

```php
'locale_currency_map' => [
    'pt_BR' => 'BRL',  // Português Brasil → Real
    'en' => 'USD',      // Inglês → Dólar
    'es' => 'EUR',      // Espanhol → Euro
    'de' => 'EUR',      // Alemão → Euro
],
```

### `app/Http/Middleware/SetLocale.php`

```php
public function handle(Request $request, Closure $next): Response
{
    // 1. Obtém locale da sessão (escolha do usuário)
    $locale = Session::get('locale', config('app.locale'));
    
    // 2. Define locale da aplicação
    App::setLocale($locale);
    
    // 3. Define moeda baseada no locale
    $currency = config("currency.locale_currency_map.{$locale}", 'BRL');
    Session::put('currency', $currency);
    
    // 4. Disponibiliza para views
    view()->share('currentCurrency', $currency);
    view()->share('currentLocale', $locale);
    
    return $next($request);
}
```

---

## 🎨 Formatação Automática por Locale

### Exemplo: Mesmo Valor, Formatos Diferentes

**Valor no banco:** `1234.567` (DECIMAL 15,3)

```php
$product = Product::find(1);
$price = $product->price; // MoneyWrapper com R$ 1234.567

// Usuário com locale pt_BR
App::setLocale('pt_BR');
echo $price->formatted(); // "R$ 1.234,57"
                          // - Ponto como separador de milhares
                          // - Vírgula como separador decimal
                          // - Símbolo antes do valor

// Usuário com locale en_US
App::setLocale('en_US');
echo $price->formatted(); // "$1,234.57"
                          // - Vírgula como separador de milhares
                          // - Ponto como separador decimal
                          // - Símbolo grudado no valor

// Usuário com locale de_DE (Alemão)
App::setLocale('de_DE');
echo $price->formatted(); // "1.234,57 €"
                          // - Ponto como separador de milhares
                          // - Vírgula como separador decimal
                          // - Símbolo depois do valor
```

---

## 🛠️ Como o Usuário Escolhe o Idioma/Locale

### Opção 1: Switcher na Interface (Recomendado)

```blade
<!-- resources/views/components/locale-switcher.blade.php -->
<div class="locale-switcher">
    <form action="{{ route('locale.change') }}" method="POST">
        @csrf
        <select name="locale" onchange="this.form.submit()">
            <option value="pt_BR" {{ app()->getLocale() === 'pt_BR' ? 'selected' : '' }}>
                🇧🇷 Português (Brasil)
            </option>
            <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>
                🇺🇸 English (US)
            </option>
            <option value="es" {{ app()->getLocale() === 'es' ? 'selected' : '' }}>
                🇪🇸 Español
            </option>
            <option value="de" {{ app()->getLocale() === 'de' ? 'selected' : '' }}>
                🇩🇪 Deutsch
            </option>
        </select>
    </form>
</div>
```

### Controller para Mudar Locale

```php
// app/Http/Controllers/LocaleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function change(Request $request)
    {
        $locale = $request->input('locale', 'pt_BR');
        
        // Valida locale
        $validLocales = ['pt_BR', 'en', 'es', 'de'];
        if (!in_array($locale, $validLocales)) {
            $locale = 'pt_BR';
        }
        
        // Salva na sessão
        Session::put('locale', $locale);
        
        return redirect()->back()->with('success', __('app.locale_changed'));
    }
}
```

### Route

```php
// routes/web.php
Route::post('/locale/change', [LocaleController::class, 'change'])->name('locale.change');
```

---

## 📊 Exemplos Práticos

### 1. View Blade - Automático

```blade
<!-- O formato muda automaticamente com base no locale do usuário -->
<div class="product-card">
    <h3>{{ $product->name }}</h3>
    <p class="price">{{ $product->price }}</p>
    <!-- pt_BR: R$ 1.234,57 -->
    <!-- en_US: $1,234.57 -->
    <!-- de_DE: 1.234,57 € -->
</div>
```

### 2. Com Locale Específico

```blade
<!-- Força um locale específico -->
<p>Preço em Reais: {{ $product->price->formatted('pt_BR') }}</p>
<p>Price in USD: {{ $product->price->formatted('en_US') }}</p>
<p>Preis in EUR: {{ $product->price->formatted('de_DE') }}</p>
```

### 3. Multi-Moeda no Mesmo Produto

```php
// Model com múltiplas moedas
class Product extends Model
{
    protected $casts = [
        'price_brl' => MonetaryCurrency::class . ':BRL',
        'price_usd' => MonetaryCurrency::class . ':USD',
        'price_eur' => MonetaryCurrency::class . ':EUR',
    ];
}

// View
<div class="prices">
    <p>🇧🇷 {{ $product->price_brl->formatted('pt_BR') }}</p>
    <!-- R$ 1.234,57 -->
    
    <p>🇺🇸 {{ $product->price_usd->formatted('en_US') }}</p>
    <!-- $234.57 -->
    
    <p>🇪🇺 {{ $product->price_eur->formatted('de_DE') }}</p>
    <!-- 199,99 € -->
</div>
```

---

## 🎯 Testes

### Testar Formatação com Diferentes Locales

```php
// tests/Feature/MoneyFormattingTest.php

public function test_formats_money_based_on_locale()
{
    $product = Product::factory()->create([
        'price' => 1234.567,
        'currency' => 'BRL',
    ]);
    
    // Teste pt_BR
    App::setLocale('pt_BR');
    $this->assertEquals('R$ 1.234,57', $product->price->formatted());
    
    // Teste en_US
    App::setLocale('en_US');
    $this->assertEquals('$1,234.57', $product->price->formatted());
    
    // Teste de_DE
    App::setLocale('de_DE');
    $this->assertStringContainsString('1.234,57', $product->price->formatted());
}

public function test_user_can_change_locale()
{
    $response = $this->post('/locale/change', ['locale' => 'en']);
    
    $this->assertEquals('en', Session::get('locale'));
    $this->assertEquals('USD', Session::get('currency'));
}
```

---

## 🔍 Como Funciona Internamente

### 1. **brick/money + ext-intl**

O `brick/money` usa a extensão PHP `intl` (International) para formatação:

```php
// Internamente, brick/money faz:
$formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
return $formatter->formatCurrency($amount, $currencyCode);
```

### 2. **Fallback Manual**

Se `ext-intl` não estiver disponível, `MoneyWrapper` usa formatação manual:

```php
private function manualFormat(string $locale): string
{
    $amount = (float)$this->toDecimal();
    $symbol = $this->getCurrencySymbol();

    // Formato brasileiro
    if (str_starts_with($locale, 'pt')) {
        return sprintf('%s %s', $symbol, number_format($amount, 2, ',', '.'));
    }

    // Formato americano/internacional
    return sprintf('%s%s', $symbol, number_format($amount, 2, '.', ','));
}
```

---

## ⚙️ Verificar se ext-intl está Instalado

```bash
# Verificar extensão intl
php -m | grep intl

# Se não estiver instalado:
# Ubuntu/Debian
sudo apt-get install php-intl

# Windows
# Edite php.ini e remova ; de:
extension=intl
```

---

## 🌐 Adicionar Novo Locale

### 1. Adicionar no Mapeamento

```php
// config/currency.php
'locale_currency_map' => [
    'pt_BR' => 'BRL',
    'en' => 'USD',
    'es' => 'EUR',
    'de' => 'EUR',
    'fr' => 'EUR',     // ✅ Novo: Francês
    'ja' => 'JPY',     // ✅ Novo: Japonês
    'zh_CN' => 'CNY',  // ✅ Novo: Chinês
],

'available' => [
    // ... existentes
    'JPY' => [
        'name' => 'Japanese Yen',
        'symbol' => '¥',
        'locale' => 'ja_JP',
    ],
    'CNY' => [
        'name' => 'Chinese Yuan',
        'symbol' => '¥',
        'locale' => 'zh_CN',
    ],
],
```

### 2. Criar Arquivos de Tradução

```bash
php artisan lang:publish

# Criar:
resources/lang/fr/app.php
resources/lang/ja/app.php
resources/lang/zh_CN/app.php
```

### 3. Atualizar Switcher

```blade
<option value="fr">🇫🇷 Français</option>
<option value="ja">🇯🇵 日本語</option>
<option value="zh_CN">🇨🇳 中文</option>
```

---

## 📱 Detectar Locale do Browser (Opcional)

```php
// app/Http/Middleware/SetLocale.php

public function handle(Request $request, Closure $next): Response
{
    // Tenta detectar do browser se não houver na sessão
    if (!Session::has('locale')) {
        $browserLocale = $request->getPreferredLanguage(['pt_BR', 'en', 'es', 'de']);
        $locale = $browserLocale ?? config('app.locale');
    } else {
        $locale = Session::get('locale');
    }
    
    App::setLocale($locale);
    // ... resto do código
}
```

---

## 🎨 Exemplo Real: Dashboard Multi-Idioma

```blade
<!-- Dashboard que adapta automaticamente -->
<div class="dashboard">
    <div class="stats">
        <div class="stat-card">
            <h3>{{ __('app.total_sales') }}</h3>
            <p class="value">{{ $totalSales }}</p>
            <!-- pt_BR: "Total de Vendas" | "R$ 125.430,50" -->
            <!-- en_US: "Total Sales" | "$25,086.10" -->
        </div>
        
        <div class="stat-card">
            <h3>{{ __('app.average_ticket') }}</h3>
            <p class="value">{{ $averageTicket }}</p>
            <!-- Formato muda automaticamente -->
        </div>
    </div>
    
    <table class="products">
        <thead>
            <tr>
                <th>{{ __('app.product') }}</th>
                <th>{{ __('app.price') }}</th>
                <th>{{ __('app.stock') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->price }}</td>
                <!-- Formato automático por locale -->
                <td>{{ $product->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

---

## ✨ Resumo

| Aspecto | Como Funciona |
|---------|---------------|
| **Detecção** | SetLocale middleware lê da sessão |
| **Formatação** | MoneyWrapper usa App::getLocale() automaticamente |
| **Moeda** | Mapeada por locale em config/currency.php |
| **Biblioteca** | brick/money + ext-intl para formatação precisa |
| **Fallback** | Formatação manual se ext-intl não disponível |
| **Escolha do Usuário** | Via switcher na interface ou query string |
| **Persistência** | Salvo na sessão do usuário |

---

## 🚀 Benefícios

1. ✅ **Automático** - Usuário escolhe idioma, formatação muda sozinha
2. ✅ **Preciso** - usa padrões internacionais (ICU)
3. ✅ **Flexível** - Pode forçar locale específico quando necessário
4. ✅ **Multi-moeda** - Suporta múltiplas moedas no mesmo produto
5. ✅ **SEO-Friendly** - Pode usar locale na URL (/pt-br/, /en/)
6. ✅ **Testável** - Fácil testar cada locale

---

**Criado em:** 17 de outubro de 2025  
**Laravel:** 12.x  
**PHP:** 8.3+  
**Biblioteca:** brick/money 0.10.3
