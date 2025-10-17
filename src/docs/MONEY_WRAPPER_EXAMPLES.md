# MoneyWrapper - Exemplos Práticos

## 🎯 Uso Simplificado no Front-End

Com o `MoneyWrapper`, você tem acesso direto a métodos convenientes sem precisar chamar `getAmount()->__toString()` toda vez.

---

## 📝 Exemplos de Uso

### 1. **Blade Views - Exibição Formatada**

```blade
{{-- ✅ MELHOR FORMA: Formatação automática com 2 casas decimais --}}
<p>Preço: {{ $product->price->formatted() }}</p>
{{-- Output: "R$ 1.234,57" --}}

{{-- Ou simplesmente converta para string --}}
<p>Preço: {{ $product->price }}</p>
{{-- Output: "R$ 1.234,57" (usa __toString()) --}}

{{-- Com locale customizado --}}
<p>Price (US): {{ $product->price->formatted('en_US') }}</p>
{{-- Output: "$1,234.57" --}}
```

### 2. **Forms Livewire - Valores para Inputs**

```php
// app/Livewire/Forms/ProductForm.php

public function setProduct(Product $product)
{
    $this->product = $product;
    $this->name = $product->name;
    
    // ✅ Usa toDecimal() para obter valor com 2 casas decimais
    $this->price = $product->price?->toDecimal();
    // Output: "1234.57"
    
    // OU para input masked (formato BR ou US)
    $this->price = $product->price?->toLocalizedDecimal();
    // Output pt_BR: "1.234,57"
    // Output en_US: "1,234.57"
}
```

### 3. **API/JSON - Serialização Automática**

```php
// Controller
public function show(Product $product)
{
    return response()->json($product);
}

// Output JSON:
{
    "id": 1,
    "name": "Notebook",
    "price": {
        "amount": "1234.57",      // 2 casas decimais
        "currency": "BRL",
        "formatted": "R$ 1.234,57",
        "symbol": "R$"
    },
    "cost_price": {
        "amount": "987.65",
        "currency": "BRL",
        "formatted": "R$ 987,65",
        "symbol": "R$"
    }
}
```

### 4. **Cálculos - Métodos Encadeados**

```php
$product = Product::find(1);

// ✅ Operações fluentes - retornam MoneyWrapper
$discount = $product->price->multipliedBy('0.1', RoundingMode::HALF_UP);
$final = $product->price->minus($discount);

// Exibe o resultado
echo $final->formatted(); // "R$ 1.111,11"
echo $final->toDecimal(); // "1111.11"
```

### 5. **Comparações**

```php
use Brick\Money\Money;
use Brick\Money\Context\CustomContext;

$product = Product::find(1);

// ✅ Comparações usando o Money interno
$threshold = Money::of(1000, 'BRL', new CustomContext(3));

if ($product->price->isGreaterThan($threshold)) {
    echo "Produto caro!";
}

if ($product->price->isZero()) {
    echo "Produto gratuito!";
}
```

---

## 🎨 Métodos Disponíveis

### **Formatação**

```php
// Formatado com símbolo e 2 casas decimais (usa locale do app)
$product->price->formatted();           // "R$ 1.234,57"

// Com locale customizado
$product->price->formatted('en_US');    // "$1,234.57"

// Apenas valor decimal (sem símbolo) - 2 casas
$product->price->toDecimal();           // "1234.57"

// Valor localizado sem símbolo
$product->price->toLocalizedDecimal();  // "1.234,57" (pt_BR)
$product->price->toLocalizedDecimal('en_US'); // "1,234.57"
```

### **Informações**

```php
// Código da moeda
$product->price->getCurrencyCode();     // "BRL"

// Símbolo da moeda
$product->price->getCurrencySymbol();   // "R$"

// Array completo para JSON
$product->price->toArray();
// [
//     'amount' => '1234.57',
//     'currency' => 'BRL',
//     'formatted' => 'R$ 1.234,57',
//     'symbol' => 'R$'
// ]
```

### **Operações Aritméticas**

```php
use Brick\Math\RoundingMode;

// Todas retornam novo MoneyWrapper
$product->price->plus($other);
$product->price->minus($other);
$product->price->multipliedBy('0.9', RoundingMode::HALF_UP);
$product->price->dividedBy(2, RoundingMode::HALF_UP);
$product->price->abs();
$product->price->negated();
```

### **Comparações**

```php
$product->price->isEqualTo($other);
$product->price->isGreaterThan($other);
$product->price->isGreaterThanOrEqualTo($other);
$product->price->isLessThan($other);
$product->price->isLessThanOrEqualTo($other);
$product->price->isZero();
$product->price->isPositive();
$product->price->isNegative();
```

### **Acesso ao Money Original**

```php
// Se precisar do Money original do brick/money
$money = $product->price->getMoney();
// Brick\Money\Money instance
```

---

## 🔧 Factory Method

Criar MoneyWrapper manualmente:

```php
use App\Support\MoneyWrapper;

// A partir de valor numérico
$wrapper = MoneyWrapper::make(1234.567, 'BRL');

// A partir de Money existente
use Brick\Money\Money;
$money = Money::of(1234.567, 'BRL');
$wrapper = MoneyWrapper::make($money);

// A partir de outro MoneyWrapper
$wrapper2 = MoneyWrapper::make($wrapper);
```

---

## 📊 Comparação: Antes vs Depois

### ❌ Antes (Código Verboso)

```php
// View
{{ $product->price->getAmount()->toScale(2, RoundingMode::HALF_UP) }}

// Livewire Form
$this->price = $product->price?->getAmount()->__toString();

// Formatação manual
$formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
echo $formatter->formatCurrency($product->price->getAmount()->toFloat(), 'BRL');
```

### ✅ Depois (Simples e Limpo)

```php
// View
{{ $product->price->formatted() }}
// ou simplesmente
{{ $product->price }}

// Livewire Form
$this->price = $product->price?->toDecimal();

// Formatação automática (já inclusa)
echo $product->price->formatted();
```

---

## 🎯 Casos de Uso Reais

### E-commerce: Exibir Preço com Desconto

```blade
<div class="product-card">
    <h3>{{ $product->name }}</h3>
    
    @if($product->discount > 0)
        <p class="original-price">
            <s>{{ $product->price->formatted() }}</s>
        </p>
        <p class="discount-price text-green-600">
            {{ $product->price->multipliedBy(1 - $product->discount / 100, RoundingMode::HALF_UP)->formatted() }}
        </p>
        <span class="badge">{{ $product->discount }}% OFF</span>
    @else
        <p class="price">{{ $product->price->formatted() }}</p>
    @endif
</div>
```

### Dashboard: Total de Vendas

```php
use Illuminate\Support\Facades\DB;
use App\Support\MoneyWrapper;

$totalSales = DB::table('orders')
    ->where('status', 'completed')
    ->sum('total');

$total = MoneyWrapper::make($totalSales, 'BRL');

echo "Total de Vendas: " . $total->formatted();
// Output: "Total de Vendas: R$ 125.430,50"
```

### API: Resposta Padronizada

```php
public function index()
{
    $products = Product::all();
    
    return response()->json([
        'data' => $products,
        'meta' => [
            'total' => $products->count(),
            // Preços já serializados automaticamente com toArray()
        ]
    ]);
}
```

---

## ✨ Benefícios

1. **Código Limpo**: Métodos intuitivos e fáceis de lembrar
2. **Type-Safe**: Mantém segurança de tipos do brick/money
3. **DRY**: Não repete lógica de formatação
4. **Locale-Aware**: Respeita configuração da aplicação
5. **API-Friendly**: Serialização JSON automática
6. **Livewire-Compatible**: Funciona perfeitamente com Livewire

---

**Criado em:** 17 de outubro de 2025  
**Laravel:** 12.x  
**PHP:** 8.3+
