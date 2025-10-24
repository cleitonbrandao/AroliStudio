# MonetaryCurrency Cast - Guia Completo

**Valores Monetários Type-Safe com Brick/Money** 🏦💰

---

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Instalação](#instalação)
3. [Configuração do Banco](#configuração-do-banco)
4. [Uso Básico](#uso-básico)
5. [Uso Avançado](#uso-avançado)
6. [API Completa](#api-completa)
7. [Best Practices](#best-practices)
8. [Troubleshooting](#troubleshooting)

---

## Visão Geral

O `MonetaryCurrency` é um cast Eloquent que usa a biblioteca [brick/money](https://github.com/brick/money) para manipulação type-safe de valores monetários, garantindo:

✅ **Precisão absoluta** - BCMath elimina erros de arredondamento de float  
✅ **Type-Safety** - Money objects garantem consistência entre valor e moeda  
✅ **Imutabilidade** - Operações retornam novas instâncias (seguro)  
✅ **Formatação embutida** - Suporte nativo a múltiplos locales  
✅ **Aritmética precisa** - Métodos seguros: `plus()`, `minus()`, `multipliedBy()`, `dividedBy()`

### Por que NÃO usar FLOAT/DOUBLE?

```php
// ❌ ERRADO: Float tem erros de precisão
$price = 0.1 + 0.2; // 0.30000000000000004 (WTF!)

// ✅ CERTO: Brick/Money com BCMath
use Brick\Money\Money;
$price = Money::of('0.1', 'BRL')->plus(Money::of('0.2', 'BRL')); 
// Money instance com valor exato 0.300
```

---

## Instalação

### 1. Instalar Brick/Money

```bash
composer require brick/money
```

### 2. Verificar Extensão BCMath (PHP)

```bash
php -m | grep bcmath
```

Se não estiver instalado, adicione no `php.ini`:
```ini
extension=bcmath
```

### 3. Cast já está disponível

O cast `MonetaryCurrency` já está criado em `app/Casts/MonetaryCurrency.php`.

---

## Configuração do Banco

### Migration Recomendada

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    
    // Valor monetário com 3 casas decimais
    $table->decimal('price', 15, 3)->default(0.000)->comment('Valor monetário');
    
    // Código ISO 4217 da moeda
    $table->string('currency', 3)->default('BRL')->comment('BRL, USD, EUR, etc.');
    
    // Preço de custo (opcional)
    $table->decimal('cost_price', 15, 3)->default(0.000);
    
    $table->timestamps();
});
```

**Por que DECIMAL(15,3)?**
- `DECIMAL` garante precisão exata (vs FLOAT que tem erros)
- `15` dígitos totais: suporta até 999.999.999.999,999
- `3` casas decimais: evita perda em cálculos (juros, divisões)

---

## Uso Básico

### 1. Configurar Model

```php
namespace App\Models;

use App\Casts\MonetaryCurrency;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'price', 'currency', 'cost_price'
    ];

    protected $casts = [
        'price' => MonetaryCurrency::class,
        'cost_price' => MonetaryCurrency::class,
    ];
}
```

### 2. Criar/Atualizar Registros

```php
use Brick\Money\Money;

// Opção 1: Money instance (recomendado)
$product = Product::create([
    'name' => 'Notebook',
    'price' => Money::of(1234.567, 'BRL'),
]);

// Opção 2: Valores numéricos
$product = Product::create([
    'name' => 'Mouse',
    'price' => 1234.567,
    'currency' => 'USD',
]);

// Opção 3: Strings formatadas
$product = Product::create([
    'name' => 'Teclado',
    'price' => 'R$ 1.234,56', // Detecta formato brasileiro
]);
```

### 3. Ler Valores (Retorna Money Instance)

```php
$product = Product::find(1);

// Retorna Brick\Money\Money instance
$price = $product->price; 

// Obter valor como string decimal
echo $price->getAmount(); // "1234.567"

// Obter código da moeda
echo $price->getCurrency()->getCurrencyCode(); // "BRL"

// Formatar para exibição
echo $price->formatTo('pt_BR'); // "R$ 1.234,57"
echo $price->formatTo('en_US'); // "$1,234.57"
```

### 4. Operações Aritméticas (Type-Safe)

```php
use Brick\Math\RoundingMode;

$product = Product::find(1);
$price = $product->price; // Money instance

// Desconto de 10%
$discount = $price->multipliedBy('0.1', RoundingMode::HALF_UP);

// Preço final
$final = $price->minus($discount);

// Adicionar taxa
$withTax = $final->plus(Money::of(10, 'BRL'));

// Comparações
if ($price->isGreaterThan(Money::of(1000, 'BRL'))) {
    echo "Produto caro!";
}

if ($price->isEqualTo(Money::of(1234.567, 'BRL'))) {
    echo "Mesmo valor!";
}
```

---

## Uso Avançado

### 1. Múltiplas Moedas no Mesmo Model

```php
class Product extends Model
{
    protected $casts = [
        'price_brl' => MonetaryCurrency::class . ':BRL',
        'price_usd' => MonetaryCurrency::class . ':USD',
        'price_eur' => MonetaryCurrency::class . ':EUR',
    ];
}

// Criar produto multi-moeda
$product = Product::create([
    'name' => 'Serviço Global',
    'price_brl' => Money::of(5000, 'BRL'),
    'price_usd' => Money::of(1000, 'USD'),
    'price_eur' => Money::of(900, 'EUR'),
]);
```

### 2. Usar Trait MonetaryCast

```php
use App\Casts\MonetaryCast;

class Invoice extends Model
{
    use MonetaryCast;

    // Define atributos monetários automaticamente
    protected array $monetaryAttributes = [
        'total',
        'subtotal',
        'discount',
        'tax',
    ];

    // OU com configuração customizada
    protected array $monetaryAttributes = [
        'total' => ['currency' => 'USD'],
        'subtotal' => [], // Usa padrão
    ];
}

// Helpers da trait
$invoice->setMonetary('total', 1234.56);
$formatted = $invoice->formatMonetary(['total', 'tax']);
$raw = $invoice->getRawMonetary('total'); // Valor decimal do banco
```

### 3. Serialização JSON/API

```php
$product = Product::find(1);

return response()->json($product);

// Retorna:
{
    "id": 1,
    "name": "Notebook",
    "price": {
        "amount": "1234.567",
        "currency": "BRL",
        "formatted": "R$ 1.234,57"
    }
}
```

### 4. Conversão Entre Moedas

```php
use Brick\Money\ExchangeRateProvider\ConfigurableProvider;
use Brick\Money\CurrencyConverter;

// Configurar taxas de câmbio
$provider = new ConfigurableProvider();
$provider->setExchangeRate('BRL', 'USD', 0.20); // 1 BRL = 0.20 USD
$provider->setExchangeRate('USD', 'BRL', 5.00); // 1 USD = 5.00 BRL

$converter = new CurrencyConverter($provider);

$priceBRL = Money::of(1000, 'BRL');
$priceUSD = $converter->convert($priceBRL, 'USD', RoundingMode::HALF_UP);

echo $priceUSD->formatTo('en_US'); // "$200.00"
```

---

## API Completa

### Métodos do Cast

#### `get()` - Converte DECIMAL do banco → Money instance
```php
$product->price; // Brick\Money\Money
```

#### `set()` - Converte Money/valor → DECIMAL para banco
```php
$product->price = Money::of(1234.567, 'BRL');
$product->price = 1234.567;
$product->price = 'R$ 1.234,56';
```

#### `serialize()` - Converte para JSON/API
```php
$product->toArray(); // ['price' => ['amount' => '1234.567', ...]]
```

### Métodos do Money Object

#### Obter Valores
```php
$price->getAmount();                    // BigDecimal object
$price->getAmount()->toFloat();          // 1234.567 (float)
$price->getAmount()->__toString();       // "1234.567" (string)
$price->getCurrency();                   // Currency object
$price->getCurrency()->getCurrencyCode(); // "BRL"
```

#### Aritmética
```php
$price->plus($other);                    // Adiciona
$price->minus($other);                   // Subtrai
$price->multipliedBy($multiplier);       // Multiplica
$price->dividedBy($divisor);             // Divide
$price->abs();                           // Valor absoluto
$price->negated();                       // Inverte sinal
```

#### Comparações
```php
$price->isEqualTo($other);               // ==
$price->isGreaterThan($other);           // >
$price->isGreaterThanOrEqualTo($other);  // >=
$price->isLessThan($other);              // <
$price->isLessThanOrEqualTo($other);     // <=
$price->isZero();                        // == 0
$price->isPositive();                    // > 0
$price->isNegative();                    // < 0
```

#### Formatação
```php
$price->formatTo('pt_BR');               // "R$ 1.234,57"
$price->formatTo('en_US');               // "$1,234.57"
$price->formatTo('de_DE');               // "1.234,57 €"
```

---

## Best Practices

### ✅ DO's

1. **Use DECIMAL(15,3) no banco**
   ```php
   $table->decimal('price', 15, 3)->default(0.000);
   ```

2. **Sempre especifique RoundingMode em operações**
   ```php
   $result = $price->multipliedBy(0.9, RoundingMode::HALF_UP);
   ```

3. **Use Money instances para cálculos**
   ```php
   $total = $price->plus($tax)->minus($discount);
   ```

4. **Formate apenas na exibição**
   ```php
   echo $price->formatTo('pt_BR'); // Views
   ```

5. **Use toRational() para cross-context operations**
   ```php
   $money1->plus($money2->toRational(), RoundingMode::HALF_UP);
   ```

### ❌ DON'Ts

1. **Nunca use FLOAT/DOUBLE**
   ```php
   $table->float('price'); // ❌ ERRADO - erros de precisão
   ```

2. **Não faça cálculos com floats**
   ```php
   $total = $price * 0.9; // ❌ ERRADO - perda de precisão
   ```

3. **Não compare floats diretamente**
   ```php
   if ($price == 1234.56) {} // ❌ ERRADO - float comparison
   ```

4. **Não armazene Money serializado**
   ```php
   $product->price_json = json_encode($money); // ❌ ERRADO
   ```

---

## Troubleshooting

### Erro: "Extension bcmath is required"

**Solução:** Instale/ative a extensão BCMath no PHP:

```bash
# Ubuntu/Debian
sudo apt-get install php-bcmath

# Windows
# Edite php.ini e remova o ; de:
extension=bcmath
```

### Erro: "RoundingNecessaryException"

**Solução:** Sempre especifique `RoundingMode`:

```php
// ❌ ERRADO
$result = $price->multipliedBy(0.9);

// ✅ CERTO
use Brick\Math\RoundingMode;
$result = $price->multipliedBy(0.9, RoundingMode::HALF_UP);
```

### Erro: "MoneyMismatchException: contexts differ"

**Solução:** Use `toRational()` para operações cross-context:

```php
// ❌ ERRADO
$money1->plus($money2); // Se contextos forem diferentes

// ✅ CERTO
$money1->plus($money2->toRational(), RoundingMode::HALF_UP);
```

### Valor NULL sendo retornado

**Solução:** Verifique se:
1. Coluna do banco está NULL
2. Formato do valor é inválido
3. Moeda está especificada corretamente

```php
// Veja os logs
Log::warning("MonetaryCurrency: ...");
```

---

## Exemplos Práticos

### E-commerce: Carrinho de Compras

```php
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class Cart
{
    protected array $items = [];
    
    public function add(Product $product, int $quantity): void
    {
        $this->items[] = [
            'product' => $product,
            'quantity' => $quantity,
        ];
    }
    
    public function getTotal(): Money
    {
        $total = Money::zero('BRL');
        
        foreach ($this->items as $item) {
            $subtotal = $item['product']->price
                ->multipliedBy($item['quantity'], RoundingMode::HALF_UP);
            
            $total = $total->plus($subtotal);
        }
        
        return $total;
    }
    
    public function applyDiscount(float $percentage): Money
    {
        $total = $this->getTotal();
        $discount = $total->multipliedBy($percentage / 100, RoundingMode::HALF_UP);
        
        return $total->minus($discount);
    }
}

// Uso
$cart = new Cart();
$cart->add($product1, 2);
$cart->add($product2, 1);

$total = $cart->getTotal();
$withDiscount = $cart->applyDiscount(10); // 10% desconto

echo $total->formatTo('pt_BR');         // "R$ 3.703,70"
echo $withDiscount->formatTo('pt_BR');  // "R$ 3.333,33"
```

### Sistema de Parcelas

```php
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class Installment
{
    public static function calculate(Money $total, int $installments): array
    {
        $currency = $total->getCurrency()->getCurrencyCode();
        
        // Valor de cada parcela
        $installmentValue = $total->dividedBy($installments, RoundingMode::HALF_UP);
        
        // Ajuste para garantir que soma seja exata
        $remainder = $total->minus(
            $installmentValue->multipliedBy($installments, RoundingMode::HALF_UP)
        );
        
        $result = [];
        for ($i = 1; $i <= $installments; $i++) {
            // Primeira parcela leva o ajuste (se houver)
            if ($i === 1 && !$remainder->isZero()) {
                $result[] = $installmentValue->plus($remainder);
            } else {
                $result[] = $installmentValue;
            }
        }
        
        return $result;
    }
}

// Uso
$total = Money::of(1000, 'BRL');
$installments = Installment::calculate($total, 3);

foreach ($installments as $index => $value) {
    echo ($index + 1) . "x de " . $value->formatTo('pt_BR') . "\n";
}

// Output:
// 1x de R$ 333,34
// 2x de R$ 333,33
// 3x de R$ 333,33
```

---

## Migração de MonetaryCorrency → MonetaryCurrency

Se você estava usando `MonetaryCorrency` (antigo cast):

### 1. Atualizar Models

```php
// Antes
protected $casts = [
    'price' => MonetaryCorrency::class,
];

// Depois
use App\Casts\MonetaryCurrency;

protected $casts = [
    'price' => MonetaryCurrency::class,
];
```

### 2. Rodar Migration

```bash
php artisan migrate
```

### 3. Atualizar Código

```php
// Antes (retornava string formatada)
$formatted = $product->price; // "R$ 1.234,56"

// Depois (retorna Money instance)
$money = $product->price; // Money object
$formatted = $money->formatTo('pt_BR'); // "R$ 1.234,57"
```

---

## Referências

- [Brick/Money GitHub](https://github.com/brick/money)
- [ISO 4217 Currency Codes](https://www.iso.org/iso-4217-currency-codes.html)
- [BCMath Documentation](https://www.php.net/manual/en/book.bc.php)
- [Laravel Custom Casts](https://laravel.com/docs/eloquent-mutators#custom-casts)

---

**Criado em:** 16 de outubro de 2025  
**Versão:** 1.0.0  
**Laravel:** 12.x  
**PHP:** 8.3+
