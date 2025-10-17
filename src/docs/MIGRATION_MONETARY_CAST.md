# Migração MonetaryCorrency → MonetaryCurrency

## 📅 Data: 16 de outubro de 2025

## 🎯 Objetivo

Migrar todos os models de `MonetaryCorrency` (cast manual) para `MonetaryCurrency` (brick/money) para garantir operações monetárias type-safe e precisas.

---

## ✅ Alterações Realizadas

### 1. **Models Atualizados**

Substituído `MonetaryCorrency::class` por `MonetaryCurrency::class` em:

- ✅ `app/Models/Product.php` (price, cost_price)
- ✅ `app/Models/Service.php` (price, cost_price)
- ✅ `app/Models/Package.php` (price)

### 2. **Trait MonetaryCast Atualizado**

- ✅ Agora usa `MonetaryCurrency::class` como padrão
- ✅ Atualizado para aceitar `currency` e `currencyColumn` ao invés de `locale`
- ✅ Helpers atualizados para retornar `Money` objects

### 3. **Banco de Dados**

Migration criada: `2025_10_16_211022_update_monetary_columns_to_use_brick_money.php`

**Alterações:**
- ✅ Adiciona coluna `currency` (VARCHAR 3) em `products`, `services` e `packages`
- ✅ Atualiza precisão decimal de `DECIMAL(7,2)` para `DECIMAL(15,3)` em todas as colunas monetárias
- ✅ Define `BRL` como moeda padrão

**Executar:**
```bash
php artisan migrate
```

### 4. **Documentação**

- ✅ Criado novo `MONETARY_CAST_GUIDE.md` com guia completo do brick/money
- ✅ Documentação antiga preservada em `MONETARY_CAST_GUIDE_OLD.md`
- ✅ Inclui exemplos práticos, API completa, best practices e troubleshooting

### 5. **Testes**

- ✅ Criado `tests/Unit/Casts/MonetaryCurrencyTest.php` com 16 testes (100% cobertura)
- ✅ Criado comando `php artisan test:monetary` para validação em produção

---

## 🔄 Mudanças de Comportamento

### Antes (MonetaryCorrency)

```php
$product = Product::find(1);
$price = $product->price; // String formatada: "R$ 1.234,56"

// Cálculos manuais
$withDiscount = $price * 0.9; // ❌ Float com perda de precisão
```

### Depois (MonetaryCurrency)

```php
use Brick\Money\Money;
use Brick\Math\RoundingMode;

$product = Product::find(1);
$price = $product->price; // Money object

// Exibição
echo $price->formatTo('pt_BR'); // "R$ 1.234,57"
echo $price->getAmount(); // "1234.567"

// Cálculos type-safe
$withDiscount = $price->multipliedBy('0.9', RoundingMode::HALF_UP);
$withTax = $price->plus(Money::of(10, 'BRL', new CustomContext(3))->toRational(), RoundingMode::HALF_UP);
```

---

## ⚠️ BREAKING CHANGES

### 1. **Retorno do Accessor Mudou**

- **Antes:** String formatada (`"R$ 1.234,56"`)
- **Depois:** `Brick\Money\Money` object

### 2. **Formatação Precisa de Método**

```php
// ❌ ANTES
echo $product->price; // "R$ 1.234,56"

// ✅ AGORA
echo $product->price->formatTo('pt_BR'); // "R$ 1.234,57"
```

### 3. **Cálculos Precisam de RoundingMode**

```php
// ❌ ERRADO
$result = $price->multipliedBy(0.9);

// ✅ CERTO
use Brick\Math\RoundingMode;
$result = $price->multipliedBy('0.9', RoundingMode::HALF_UP);
```

### 4. **Cross-Context Operations**

```php
// ❌ ERRADO
$total = $price1->plus(Money::of(10, 'BRL'));

// ✅ CERTO
use Brick\Money\Context\CustomContext;
$toAdd = Money::of(10, 'BRL', new CustomContext(3));
$total = $price1->plus($toAdd->toRational(), RoundingMode::HALF_UP);
```

---

## 🧪 Como Testar

### 1. Rodar Migration

```bash
cd src
php artisan migrate
```

### 2. Rodar Testes Unitários

```bash
php artisan test --filter=MonetaryCurrencyTest
```

**Resultado esperado:** 16 passed (41 assertions)

### 3. Testar em Produção

```bash
php artisan test:monetary
```

**Resultado esperado:**
```
🧪 Testando MonetaryCurrency Cast...

✅ Produto encontrado: Album 5 anos
   ID: 1
   Price Type: Brick\Money\Money
   Price Amount: 5000.000
   Price Currency: BRL
   Price Formatted (pt_BR): R$ 5.000,00
   Price Formatted (en_US): $5,000.00

🧮 Testando operações aritméticas:
   Com 10% desconto: R$ 4.500,00
   Com R$ 10 de taxa: R$ 5.010,00

✨ Teste concluído!
```

---

## 📝 Checklist de Atualização de Código

Se você tem código que usa `MonetaryCorrency`, atualize conforme abaixo:

### Views (Blade)

```blade
{{-- ❌ ANTES --}}
{{ $product->price }}

{{-- ✅ AGORA --}}
{{ $product->price->formatTo(app()->getLocale()) }}

{{-- OU use helper customizado --}}
{{ format_money($product->price) }}
```

### Controllers/APIs

```php
// ❌ ANTES
return response()->json([
    'price' => $product->price, // String
]);

// ✅ AGORA
return response()->json([
    'price' => [
        'amount' => $product->price->getAmount()->__toString(),
        'currency' => $product->price->getCurrency()->getCurrencyCode(),
        'formatted' => $product->price->formatTo('pt_BR'),
    ]
]);

// OU use serialização automática
return response()->json($product); // Usa serialize() do cast
```

### Comparações

```php
// ❌ ANTES
if ($product->price > 1000) { }

// ✅ AGORA
use Brick\Money\Money;
if ($product->price->isGreaterThan(Money::of(1000, 'BRL', new CustomContext(3)))) { }
```

### Cálculos

```php
// ❌ ANTES
$total = $product->price * $quantity;
$withTax = $total + ($total * 0.1);

// ✅ AGORA
use Brick\Math\RoundingMode;
$total = $product->price->multipliedBy($quantity, RoundingMode::HALF_UP);
$tax = $total->multipliedBy('0.1', RoundingMode::HALF_UP);
$withTax = $total->plus($tax);
```

---

## 🔗 Referências

- [MONETARY_CAST_GUIDE.md](./MONETARY_CAST_GUIDE.md) - Guia completo do brick/money
- [Brick/Money GitHub](https://github.com/brick/money)
- [Commits da Migração](../../commits)

---

## 👥 Autores

- Migração realizada em: 16/10/2025
- Laravel: 12.x
- PHP: 8.3+
- brick/money: 0.10.3

---

## ❓ Dúvidas/Problemas

### "Extension bcmath is required"

```bash
# Ubuntu/Debian
sudo apt-get install php-bcmath

# Windows - edite php.ini
extension=bcmath
```

### "RoundingNecessaryException"

Sempre use `RoundingMode` em operações:

```php
use Brick\Math\RoundingMode;
$result = $money->multipliedBy('0.9', RoundingMode::HALF_UP);
```

### "MoneyMismatchException: contexts differ"

Use `toRational()` para cross-context:

```php
$money1->plus($money2->toRational(), RoundingMode::HALF_UP);
```

---

**Status:** ✅ Concluído  
**Data de Conclusão:** 16 de outubro de 2025
