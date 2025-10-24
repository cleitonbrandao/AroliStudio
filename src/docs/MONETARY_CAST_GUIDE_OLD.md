# MonetaryCorrency Cast - Guia Completo

## 📖 Visão Geral

O `MonetaryCorrency` é um cast Eloquent avançado para lidar com valores monetários em aplicações Laravel, com suporte a múltiplos locales, precisão decimal e parsing inteligente.

---

## 🎯 Características

- ✅ **Parsing Inteligente**: Detecta automaticamente formatos brasileiro e americano
- ✅ **Precisão**: Usa 3 casas decimais (DECIMAL(15,3)) para evitar perda em cálculos
- ✅ **Segurança**: Retorna `null` ao invés de exception para valores inválidos
- ✅ **Multi-locale**: Suporta pt_BR, en_US e qualquer locale via NumberFormatter
- ✅ **Flexível**: Trait para uso simplificado ou cast direto
- ✅ **Testado**: 100% de cobertura com 14 testes unitários

---

## 📦 Instalação

### 1. Migration (Banco de Dados)

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            // ✅ CORRETO: DECIMAL(15,3) para valores monetários
            $table->decimal('price', 15, 3)->default(0.000)->comment('Preço de venda');
            $table->decimal('cost_price', 15, 3)->default(0.000)->comment('Preço de custo');
            $table->decimal('discount', 15, 3)->nullable()->comment('Desconto aplicado');
            
            // ❌ ERRADO: Nunca use FLOAT ou DOUBLE para dinheiro
            // $table->float('price'); // 0.1 + 0.2 != 0.3 em float!
            
            $table->timestamps();
        });
    }
};
```

**Por quê DECIMAL(15,3)?**
- **15 dígitos totais**: Suporta valores até R$ 999.999.999.999,999
- **3 casas decimais**: Evita perda de precisão em:
  - Divisões: R$ 10,00 / 3 = R$ 3,333
  - Juros compostos: (valor * 1.05) repetidamente
  - Conversão de moedas: USD 100 * 5.437 = BRL 543,700

---

## 🚀 Uso Básico

### Opção 1: Cast Direto no Model

```php
<?php

namespace App\Models;

use App\Casts\MonetaryCorrency;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'cost_price', 'discount'];
    
    protected $casts = [
        'price' => MonetaryCorrency::class,
        'cost_price' => MonetaryCorrency::class,
        'discount' => MonetaryCorrency::class,
    ];
}
```

### Opção 2: Usando a Trait (Recomendado)

```php
<?php

namespace App\Models;

use App\Casts\MonetaryCast;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use MonetaryCast;
    
    protected $fillable = ['name', 'price', 'cost_price', 'discount'];
    
    // Defina quais atributos são monetários
    protected array $monetaryAttributes = ['price', 'cost_price', 'discount'];
}
```

---

## 💻 Exemplos de Uso

### Criando Registros

```php
use App\Models\Product;

// ✅ Formato brasileiro
$product = Product::create([
    'name' => 'Notebook',
    'price' => '1.234,56',          // Será salvo como 1234.560
    'cost_price' => 'R$ 987,65',    // Remove símbolo automaticamente
    'discount' => '50,00',
]);

// ✅ Formato americano
$product = Product::create([
    'name' => 'Mouse',
    'price' => '1,234.56',          // Será salvo como 1234.560
    'cost_price' => '$987.65',
    'discount' => '50.00',
]);

// ✅ Valores numéricos
$product = Product::create([
    'name' => 'Teclado',
    'price' => 234.56,              // Será salvo como 234.560
    'cost_price' => 123.456,        // Mantém 3 casas decimais
]);

// ✅ Valores nulos (seguro)
$product = Product::create([
    'name' => 'Item sem preço',
    'price' => null,                // Aceito
    'cost_price' => '',             // Retorna null
    'discount' => 'abc',            // Retorna null (loga warning)
]);
```

### Lendo Valores

```php
$product = Product::find(1);

// Accessor retorna string formatada com moeda
echo $product->price;               // "R$ 1.234,56" (pt_BR)
echo $product->price;               // "$1,234.56" (en_US)

// Para obter float puro (cálculos):
$rawPrice = $product->getRawMonetary('price');  // 1234.560 (float)

// Para formatar múltiplos atributos:
$formatted = $product->formatMonetary(['price', 'cost_price']);
// ['price' => 'R$ 1.234,56', 'cost_price' => 'R$ 987,65']
```

### Atualizando Valores

```php
$product = Product::find(1);

// ✅ Usando setMonetary (passa pelo cast)
$product->setMonetary('price', '2.500,00')
        ->setMonetary('discount', '100,50')
        ->save();

// ✅ Atribuição direta também funciona
$product->price = '3.000,00';
$product->save();

// ✅ Update em massa
$product->update([
    'price' => '1.500,00',
    'cost_price' => '800,00',
]);
```

---

## 🔧 Configuração Avançada

### Cast com Moeda/Locale Customizado

```php
class Product extends Model
{
    use MonetaryCast;
    
    protected array $monetaryAttributes = [
        // Usa configuração padrão (sessão/app)
        'price' => [],
        
        // Força USD sempre
        'price_usd' => [
            'currency' => 'USD',
            'locale' => 'en_US',
        ],
        
        // Força EUR
        'price_eur' => [
            'currency' => 'EUR',
            'locale' => 'de_DE',
        ],
    ];
}
```

### Sistema Multi-idioma

```php
// No middleware SetLocale:
App::setLocale($locale);              // pt_BR ou en
Session::put('currency', $currency);  // BRL ou USD

// Os casts automaticamente usarão o locale/currency da sessão
$product = Product::find(1);
echo $product->price;  // Formatado de acordo com sessão
```

---

## 🧮 Cálculos com Precisão

```php
use App\Models\Product;

$product = Product::find(1);

// ❌ ERRADO: Usar valores formatados em cálculos
$total = (float)str_replace(',', '.', $product->price) * 3;  // Gambiarra!

// ✅ CORRETO: Usar getRawMonetary()
$rawPrice = $product->getRawMonetary('price');  // 1234.560 (float)
$total = $rawPrice * 3;                         // 3703.680

// ✅ MELHOR: Usar BCMath para precisão máxima
$rawPrice = (string)$product->getRawMonetary('price');
$total = bcmul($rawPrice, '3', 3);  // "3703.680" (string)

// Salvar resultado
$order->update([
    'total' => $total,  // Cast converte automaticamente
]);
```

---

## 📊 Formatos Suportados

### Entrada (set - salvando no banco)

| Formato               | Exemplo       | Resultado Float |
|-----------------------|---------------|-----------------|
| Brasileiro c/ milhares| "1.234,56"    | 1234.560        |
| Brasileiro s/ milhares| "234,56"      | 234.560         |
| Brasileiro 3 decimais | "12,345"      | 12.345          |
| Americano c/ milhares | "1,234.56"    | 1234.560        |
| Americano s/ milhares | "234.56"      | 234.560         |
| Com símbolo BRL       | "R$ 1.234,56" | 1234.560        |
| Com símbolo USD       | "$1,234.56"   | 1234.560        |
| Com símbolo EUR       | "€ 1.234,56"  | 1234.560        |
| Negativo BR           | "-1.234,56"   | -1234.560       |
| Negativo US           | "-1,234.56"   | -1234.560       |
| Numérico puro         | 1234.56       | 1234.560        |
| String numérica       | "1234.56"     | 1234.560        |
| Valor pequeno         | "0,001"       | 0.001           |
| Zero                  | "0"           | 0.000           |
| Null/vazio            | null / ""     | null            |
| Inválido              | "abc"         | null            |

### Saída (get - exibindo)

| Locale | Currency | Valor (banco) | Resultado Formatado |
|--------|----------|---------------|---------------------|
| pt_BR  | BRL      | 1234.560      | "R$ 1.234,56"       |
| en_US  | USD      | 1234.560      | "$1,234.56"         |
| de_DE  | EUR      | 1234.560      | "1.234,56 €"        |

---

## 🧪 Testes

```bash
# Executar testes do cast
php artisan test --filter=MonetaryCorrencyTest

# Resultado esperado:
# ✓ 14 testes passando
# ✓ 33 asserções
# ✓ Cobertura 100%
```

---

## ⚠️ Best Practices

### ✅ DO (Faça)

```php
// ✅ Use DECIMAL no banco
$table->decimal('price', 15, 3);

// ✅ Use MonetaryCorrency cast
protected $casts = ['price' => MonetaryCorrency::class];

// ✅ Use getRawMonetary() para cálculos
$raw = $product->getRawMonetary('price');

// ✅ Use BCMath para precisão crítica
$result = bcmul((string)$raw, '1.05', 3);

// ✅ Trate null explicitamente
if ($product->discount !== null) {
    // ...
}
```

### ❌ DON'T (Não faça)

```php
// ❌ Nunca use FLOAT/DOUBLE
$table->float('price');  // 0.1 + 0.2 = 0.30000000000000004

// ❌ Não converta manualmente
$price = str_replace(',', '.', $input);  // Gambiarra!

// ❌ Não use float em cálculos monetários críticos
$total = $product->price * $quantity;  // Impreciso!

// ❌ Não ignore null
$discount = $product->discount ?? 0;  // Pode causar bugs sutis
```

---

## 📚 Referências

- [MySQL DECIMAL vs FLOAT](https://dev.mysql.com/doc/refman/8.0/en/precision-math-decimal-characteristics.html)
- [PHP BCMath](https://www.php.net/manual/en/book.bc.php)
- [NumberFormatter (Intl)](https://www.php.net/manual/en/class.numberformatter.php)
- [Laravel Custom Casts](https://laravel.com/docs/eloquent-mutators#custom-casts)

---

## 🐛 Troubleshooting

### Problema: Valores sendo salvos incorretamente

**Solução**: Verifique a migração usa `DECIMAL(15,3)` e não `FLOAT`/`DOUBLE`.

### Problema: Extension `intl` não disponível

**Solução**: Instale php-intl:
```bash
# Ubuntu/Debian
sudo apt-get install php-intl

# macOS (Homebrew)
brew install php-intl

# Windows: descomentar em php.ini
extension=intl
```

### Problema: Valores formatados não mudam com locale

**Solução**: Limpe o cache:
```bash
php artisan config:clear
php artisan cache:clear
```

---

**Desenvolvido com ❤️ para AroliStudio**
