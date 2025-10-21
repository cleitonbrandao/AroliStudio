# 💰 Boas Práticas para MonetaryCurrency nas Views

## ❌ Problema Identificado

Algumas views estavam usando **métodos diferentes** para exibir valores monetários:

```blade
<!-- Product: CORRETO -->
{{ $product->price->formatted() }}

<!-- Service e Package: INCONSISTENTE (usava __toString implícito) -->
{{ $service->price }}
{{ $package->price }}
```

Embora `__toString()` também chame `formatted()` internamente, é melhor ser **explícito** para:

- ✅ Maior clareza do código
- ✅ Evitar confusão sobre qual método está sendo chamado
- ✅ Facilitar debugging
- ✅ Permitir passar parâmetros customizados (locale diferente)

## ✅ Padrão Correto

### 1. **Exibição com Locale do Usuário (RECOMENDADO)**

```blade
<!-- Formata com locale da aplicação (pt_BR = R$ 1.234,57) -->
{{ $product->price->formatted() }}
{{ $service->price->formatted() }}
{{ $package->price->formatted() }}
```

### 2. **Exibição com Locale Customizado**

```blade
<!-- Força locale específico -->
{{ $product->price->formatted('en_US') }}  <!-- $1,234.57 -->
{{ $product->price->formatted('pt_BR') }}  <!-- R$ 1.234,57 -->
```

### 3. **Apenas Valor (sem símbolo) para Inputs**

```blade
<!-- Para inputs numéricos -->
<input type="text" value="{{ $product->price->toDecimal() }}">
<!-- Resultado: 1234.57 -->

<!-- Para inputs masked (formato brasileiro) -->
<input type="text" value="{{ $product->price->toLocalizedDecimal() }}">
<!-- Resultado: 1.234,57 -->
```

### 4. **Componentes Livewire**

```blade
<!-- Em components Livewire -->
<div>
    <span>Preço: {{ $this->product->price->formatted() }}</span>
</div>

<!-- Ou com wire:model -->
<input type="text" wire:model="price" value="{{ $product->price->toDecimal() }}">
```

### 5. **APIs e JSON**

```php
// No controller
return response()->json([
    'product' => [
        'id' => $product->id,
        'price' => $product->price->toArray(), // Retorna array completo
    ]
]);

// Resultado JSON:
{
  "product": {
    "id": 1,
    "price": {
      "amount": "1234.57",
      "currency": "BRL",
      "formatted": "R$ 1.234,57",
      "symbol": "R$"
    }
  }
}
```

## 📋 Checklist de Validação

Após fazer mudanças nas views, sempre:

- [ ] Limpar cache: `php artisan optimize:clear`
- [ ] Testar em desenvolvimento local
- [ ] Verificar se o locale está correto: `App::getLocale()`
- [ ] Conferir se `ext-intl` está habilitada no PHP
- [ ] Verificar logs de erro: `storage/logs/laravel.log`

## 🔍 Debugging

Se a formatação não estiver correta:

```php
// No Tinker ou em uma rota de teste
php artisan tinker

>>> $product = App\Models\Product::first();
>>> $product->price->formatted();          // Deve retornar "R$ 1.234,57"
>>> $product->price->getCurrencyCode();     // Deve retornar "BRL"
>>> app()->getLocale();                     // Deve retornar "pt_BR"
>>> config('app.locale');                   // Deve retornar "pt_BR"
```

## 🚨 Erros Comuns

### 1. **Valor aparece sem formatação**

**Causa:** Cache de views não foi limpo.

**Solução:**

```bash
php artisan view:clear
php artisan optimize:clear
```

### 2. **Formatação americana em vez de brasileira**

**Causa:** Locale não está configurado como `pt_BR`.

**Solução:** Verificar `config/app.php`:

```php
'locale' => 'pt_BR',
'fallback_locale' => 'pt_BR',
```

### 3. **Erro "Call to undefined method"**

**Causa:** Atributo não está castado como `MonetaryCurrency`.

**Solução:** Verificar `$casts` no model:

```php
protected $casts = [
    'price' => MonetaryCurrency::class,
];
```

### 4. **Valor retorna NULL**

**Causa:** Valor no banco de dados é NULL ou vazio.

**Solução:** Use null-safe operator:

```blade
{{ $product->price?->formatted() ?? 'N/A' }}
```

## 📝 Convenções de Nomenclatura

Para manter consistência no projeto:

| Tipo             | Método                 | Uso                               |
| ---------------- | ---------------------- | --------------------------------- |
| **Display**      | `formatted()`          | Exibir para usuário (com símbolo) |
| **Input**        | `toDecimal()`          | Input numérico puro (1234.57)     |
| **Masked Input** | `toLocalizedDecimal()` | Input formatado (1.234,57)        |
| **API**          | `toArray()`            | JSON com todos os dados           |
| **Cálculos**     | `getMoney()`           | Operações com Brick\Money         |

## 🎯 Arquivos Corrigidos

- ✅ `resources/views/livewire/service/products-pagination.blade.php`
- ✅ `resources/views/livewire/service/services-pagination.blade.php`
- ✅ `resources/views/livewire/service/packages-pagination.blade.php`

Todos agora usam `->formatted()` explicitamente.

---

**Data da correção:** 21/10/2025  
**Autor:** GitHub Copilot
