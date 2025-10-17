# Money Input Component - Guia Completo

## 🎯 Visão Geral

Componente Blade para inputs monetários com formatação automática baseada no locale do usuário.

## ✨ Características

- ✅ **Símbolo dinâmico:** Mostra R$, $, €, £ ou ¥ baseado no locale
- ✅ **Formatação automática:** Digite `12345` e veja `R$ 123,45` ou `$123.45`
- ✅ **Locale-aware:** Formato brasileiro (1.234,56) ou internacional (1,234.56)
- ✅ **Integração Livewire:** Sincronização bidirecional com Alpine.js
- ✅ **Dark mode:** Suporte completo
- ✅ **Acessibilidade:** Labels, placeholders e validação

## 📦 Uso Básico

```blade
<x-money-input 
    id="price" 
    wire-model="form.price"
    placeholder="0,00"
/>
```

## 🔧 Props Disponíveis

| Prop | Tipo | Padrão | Descrição |
|------|------|--------|-----------|
| `id` | string | '' | ID do input |
| `name` | string | '' | Nome do input (para forms HTML) |
| `wireModel` | string | '' | Wire model Livewire (ex: "form.price") |
| `placeholder` | string | '' | Placeholder do input |
| `required` | boolean | false | Se o campo é obrigatório |
| `currencySymbol` | string | null | Símbolo customizado (sobrescreve o locale) |

## 💡 Exemplos Práticos

### Exemplo 1: Formulário de Produto

```blade
<div class="w-full md:w-1/2 p-2">
    <x-label for="price" value="{{ __('Preço') }}" />
    <x-money-input 
        id="price" 
        wire-model="form.price"
        placeholder="0,00"
        required
    />
    @error('form.price') 
        <span class="text-red-600 text-sm">{{ $message }}</span> 
    @enderror
</div>
```

### Exemplo 2: Símbolo Customizado

```blade
<x-money-input 
    id="salary" 
    wire-model="employee.salary"
    placeholder="0,00"
    currency-symbol="US$"
/>
```

### Exemplo 3: Múltiplos Inputs

```blade
<div class="flex gap-4">
    <div class="flex-1">
        <x-label for="price" value="Preço de Venda" />
        <x-money-input id="price" wire-model="product.price" />
    </div>
    
    <div class="flex-1">
        <x-label for="cost" value="Preço de Custo" />
        <x-money-input id="cost" wire-model="product.cost" />
    </div>
</div>
```

## 🔄 Comportamento de Formatação

### Entrada do Usuário

| Digita | Display (pt_BR) | Display (en) | Valor Wire |
|--------|-----------------|--------------|------------|
| 1 | R$ 0,01 | $0.01 | "0.01" |
| 12 | R$ 0,12 | $0.12 | "0.12" |
| 123 | R$ 1,23 | $1.23 | "1.23" |
| 1234 | R$ 12,34 | $12.34 | "12.34" |
| 12345 | R$ 123,45 | $123.45 | "123.45" |
| 123456 | R$ 1.234,56 | $1,234.56 | "1234.56" |
| 1234567 | R$ 12.345,67 | $12,345.67 | "12345.67" |

### Carregamento de Dados (Backend → Frontend)

```php
// No Livewire Component ou Form
public function setProduct(Product $product)
{
    $locale = app()->getLocale();
    $isPortuguese = str_starts_with($locale, 'pt');
    
    // Obtém o valor decimal
    $priceDecimal = $product->price?->toDecimal(); // "1234.56"
    
    // Formata para o locale
    if ($priceDecimal) {
        $this->price = $isPortuguese 
            ? number_format((float)$priceDecimal, 2, ',', '.')
            : number_format((float)$priceDecimal, 2, '.', ',');
    }
}
```

## 🎨 Mapeamento de Símbolos

| Locale | Moeda | Símbolo | Formato Número |
|--------|-------|---------|----------------|
| pt_BR | BRL | R$ | 1.234,56 |
| en | USD | $ | 1,234.56 |
| es | EUR | € | 1.234,56 |
| de | EUR | € | 1.234,56 |

## 🔍 Como Funciona Internamente

### 1. Inicialização
```javascript
init() {
    // Formata o valor inicial que vem do Livewire
    this.displayValue = this.formatMoney(this.value || '');
    
    // Observa mudanças no backend (atualização de dados)
    this.$watch('value', (newVal) => {
        if (document.activeElement !== this.$refs.input) {
            this.displayValue = this.formatMoney(newVal || '');
        }
    });
}
```

### 2. Input do Usuário
```javascript
handleInput(event) {
    // Remove tudo que não é número
    let numbersOnly = input.replace(/\D/g, '');
    
    // Trata como centavos
    let cents = parseInt(numbersOnly);
    let decimal = (cents / 100).toFixed(2);
    
    // Atualiza Livewire (formato decimal)
    this.value = decimal; // "123.45"
    
    // Atualiza display (formato localizado)
    this.displayValue = this.formatMoney(decimal); // "R$ 123,45"
}
```

### 3. Blur (Saída do Campo)
```javascript
handleBlur() {
    // Garante formatação final
    this.displayValue = this.formatMoney(this.value || '');
}
```

## 🔗 Integração com Livewire Form

### Form Object

```php
class ProductForm extends Form
{
    public $price;
    public $cost_price;
    
    public function store()
    {
        Product::create([
            'price' => $this->sanitizeMoneyValue($this->price),
            'cost_price' => $this->sanitizeMoneyValue($this->cost_price),
        ]);
    }
    
    private function sanitizeMoneyValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        // Remove símbolos e converte para decimal
        $value = preg_replace('/[R\$€£¥\s]/u', '', $value);
        
        // Detecta formato e converte
        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');
        
        if ($lastComma !== false && $lastDot !== false) {
            if ($lastComma > $lastDot) {
                // BR: 1.234,56 → 1234.56
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                // US: 1,234.56 → 1234.56
                $value = str_replace(',', '', $value);
            }
        } elseif ($lastComma !== false) {
            // 1234,56 → 1234.56
            $value = str_replace(',', '.', $value);
        }
        
        return is_numeric($value) ? (string) $value : null;
    }
}
```

## ⚠️ Importantes Considerações

### 1. Entangle com `.live`
```blade
<!-- Correto: Sincronização em tempo real -->
x-data="{ value: @entangle($wireModel).live }"

<!-- Errado: Pode causar dessincronização -->
x-data="{ value: @entangle($wireModel) }"
```

### 2. Formato do Valor Wire
O componente **sempre** envia valores no formato decimal:
```php
// ✅ Correto
$this->price = "1234.56"; // Decimal (ponto)

// ❌ Errado
$this->price = "1.234,56"; // Formatado (não processe assim no backend)
```

### 3. Validação
```php
public function rules(): array
{
    return [
        'price' => ['nullable', 'numeric', 'min:0'],
        'cost_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
    ];
}
```

## 🐛 Troubleshooting

### Problema: Valor não atualiza no Livewire

**Solução:** Verifique se está usando `.live` no entangle:
```blade
x-data="{ value: @entangle($wireModel).live }"
```

### Problema: Formato errado ao editar

**Solução:** Certifique-se de formatar corretamente no `setProduct()`:
```php
$this->price = $isPortuguese 
    ? number_format((float)$priceDecimal, 2, ',', '.')
    : number_format((float)$priceDecimal, 2, '.', ',');
```

### Problema: Símbolo errado

**Solução:** Verifique o mapeamento em `config/currency.php`:
```php
'locale_currency_map' => [
    'pt_BR' => 'BRL',
    'en' => 'USD',
    // ...
]
```

### Problema: Milhares não aparecem

**Solução:** O componente formata automaticamente. Digite `123456` e verá `R$ 1.234,56`.

## 🎯 Testes

### Teste Manual Rápido

1. **Digite:** `12345`
2. **Esperado (pt_BR):** `R$ 123,45`
3. **Esperado (en):** `$123.45`
4. **Valor Livewire:** `"123.45"`

### Teste de Edição

1. **Produto no banco:** `price = 1234.567`
2. **Load no form:** `R$ 1.234,57` ou `$1,234.57`
3. **Altere para:** `56789`
4. **Display:** `R$ 567,89` ou `$567.89`
5. **Salva:** `567.89`

## 📚 Referências

- [Alpine.js Entangle](https://livewire.laravel.com/docs/alpine#sharing-state)
- [Livewire Wire Models](https://livewire.laravel.com/docs/properties#data-binding)
- [Intl NumberFormat](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat)

## 🚀 Melhorias Futuras

- [ ] Suporte para mais moedas (CAD, AUD, CHF)
- [ ] Modo "milhões" para valores grandes
- [ ] Validação inline (min/max)
- [ ] Histórico de valores (undo/redo)
- [ ] Calculadora integrada
- [ ] Cópia formatada para clipboard
