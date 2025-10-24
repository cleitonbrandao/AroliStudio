# Moeda vs Locale: Entendendo a Diferença

## 🎯 Problema Comum

**Situação:** Você troca o idioma para inglês mas o preço continua mostrando "R$" ao invés de "$".

**Por quê?** Porque **moeda** e **locale** são conceitos diferentes!

## 📚 Conceitos

### Moeda (Currency)
- **O que é:** O código da moeda armazenado no banco de dados (BRL, USD, EUR)
- **Onde está:** Coluna `currency` na tabela `products`
- **Quando é definido:** Ao criar/editar o produto
- **Exemplo:** Um produto criado no Brasil tem `currency='BRL'`

### Locale
- **O que é:** Preferência de idioma/formato do usuário (pt_BR, en, es, de)
- **Onde está:** Sessão do usuário (`App::getLocale()`)
- **Quando é definido:** Quando o usuário troca o idioma no seletor
- **Exemplo:** Usuário escolhe inglês = `locale='en'`

## 🔍 O Comportamento Atual

### Método `formatted()`
```php
// Usa a MOEDA DO BANCO + formato do LOCALE
{{ $product->price->formatted() }}

// Produto com currency='BRL' no banco:
// - Locale pt_BR: "R$ 1.234,57" ✅ (moeda BRL + formato BR)
// - Locale en:    "R$ 1,234.57" ⚠️  (moeda BRL + formato US)
// - Locale de:    "1.234,57 R$" ⚠️  (moeda BRL + formato DE)
```

**O que acontece:**
- ✅ Formato dos números muda (vírgulas, pontos, posição)
- ❌ Símbolo da moeda NÃO muda (continua R$ porque está no banco)

### Método `formattedWithLocaleCurrency()`
```php
// Usa a MOEDA DO LOCALE + formato do LOCALE
{{ $product->price->formattedWithLocaleCurrency() }}

// Produto com currency='BRL' no banco:
// - Locale pt_BR: "R$ 1.234,57" ✅ (moeda BRL + formato BR)
// - Locale en:    "$1,234.57"   ✅ (moeda USD + formato US)
// - Locale de:    "1.234,57 €"  ✅ (moeda EUR + formato DE)
```

**O que acontece:**
- ✅ Formato dos números muda
- ✅ Símbolo da moeda muda (baseado no mapeamento locale → moeda)
- ⚠️ **NÃO faz conversão de câmbio** (só muda o símbolo!)

## 🎨 Exemplos Visuais

### Cenário: Produto com preço 1234.57 BRL

| Locale | formatted() | formattedWithLocaleCurrency() |
|--------|-------------|-------------------------------|
| pt_BR  | R$ 1.234,57 | R$ 1.234,57 |
| en     | R$ 1,234.57 | $1,234.57 |
| es     | 1.234,57 R$ | 1.234,57 € |
| de     | 1.234,57 R$ | 1.234,57 € |

## 🛠️ Qual Usar?

### Use `formatted()` quando:
- ✅ Você tem produtos com moedas diferentes no banco
- ✅ Quer manter a moeda original do produto
- ✅ Precisa de precisão na moeda (relatórios, notas fiscais)
- ✅ Exemplo: E-commerce internacional com produtos em USD, EUR, BRL

```blade
<td>{{ $product->price->formatted() }}</td>
<!-- Mostra: "R$ 1.234,57" ou "US$ 1,234.57" dependendo do banco -->
```

### Use `formattedWithLocaleCurrency()` quando:
- ✅ Todos os produtos têm a mesma moeda no banco
- ✅ Quer melhorar UX mostrando a moeda do idioma escolhido
- ✅ O valor é apenas informativo (não precisa de conversão real)
- ✅ Exemplo: Sistema interno onde os valores são sempre os mesmos

```blade
<td>{{ $product->price->formattedWithLocaleCurrency() }}</td>
<!-- Mostra: "R$ 1.234,57" ou "$1,234.57" dependendo do idioma -->
```

## ⚠️ IMPORTANTE: Não há Conversão de Câmbio!

```php
// ❌ ERRADO: Pensar que isso converte R$ 100 para $20 USD
$product->price->formattedWithLocaleCurrency(); // Mostra "$100.00"

// O valor continua sendo 100, só o SÍMBOLO muda!
```

Se você precisa de **conversão real de câmbio**, você precisa:

1. **API de taxas de câmbio** (Ex: exchangerate-api.com, fixer.io)
2. **Tabela de taxas no banco**
3. **Lógica de conversão:**

```php
// Exemplo de conversão real (você precisa implementar isso)
public function convertCurrency(string $targetCurrency): MoneyWrapper
{
    $rate = CurrencyRate::getRate($this->getCurrencyCode(), $targetCurrency);
    $convertedAmount = $this->toDecimal() * $rate;
    
    return MoneyWrapper::make($convertedAmount, $targetCurrency);
}
```

## 🔧 Mapeamento Locale → Moeda

Configurado em `config/currency.php`:

```php
'locale_currency_map' => [
    'pt_BR' => 'BRL',  // Brasil = Real
    'en'    => 'USD',  // Inglês = Dólar
    'es'    => 'EUR',  // Espanhol = Euro
    'de'    => 'EUR',  // Alemão = Euro
]
```

## 📋 Checklist: Qual Método Usar?

```
[ ] Meus produtos têm moedas diferentes? 
    → SIM: Use formatted()
    → NÃO: Continue...

[ ] Quero mostrar o símbolo da moeda do idioma escolhido?
    → SIM: Use formattedWithLocaleCurrency()
    → NÃO: Use formatted()

[ ] Preciso de conversão de câmbio real?
    → SIM: Implemente um serviço de conversão
    → NÃO: Use formattedWithLocaleCurrency()
```

## 🚀 Recomendação para Seu Caso

Baseado na sua pergunta, você provavelmente quer usar:

```blade
<!-- Em products-pagination.blade.php -->
<td class="px-6 py-4">
    {{ $product->price->formattedWithLocaleCurrency() }}
</td>
```

Isso vai:
- ✅ Mostrar "R$ 1.234,57" quando idioma for português
- ✅ Mostrar "$1,234.57" quando idioma for inglês
- ✅ Mostrar "1.234,57 €" quando idioma for alemão/espanhol
- ⚠️ Mas lembre-se: **não faz conversão de câmbio!**

## 📝 Exemplo Completo

```php
// Produto no banco: price=1234.567, currency='BRL'

// Usuário escolhe português (pt_BR)
App::setLocale('pt_BR');
echo $product->price->formatted();                      // "R$ 1.234,57"
echo $product->price->formattedWithLocaleCurrency();   // "R$ 1.234,57"

// Usuário troca para inglês (en)
App::setLocale('en');
echo $product->price->formatted();                      // "R$ 1,234.57" (BRL com formato US)
echo $product->price->formattedWithLocaleCurrency();   // "$1,234.57"   (USD com formato US)

// Usuário troca para alemão (de)
App::setLocale('de');
echo $product->price->formatted();                      // "1.234,57 R$" (BRL com formato DE)
echo $product->price->formattedWithLocaleCurrency();   // "1.234,57 €"  (EUR com formato DE)
```

## 🎓 Resumo

| Aspecto | formatted() | formattedWithLocaleCurrency() |
|---------|-------------|------------------------------|
| Moeda | Do banco | Do locale |
| Formato | Do locale | Do locale |
| Conversão | ❌ Não | ❌ Não |
| Usa currency do banco | ✅ Sim | ❌ Não |
| UX multiidioma | ⚠️ Parcial | ✅ Completo |
| Precisão monetária | ✅ Alta | ⚠️ Informativo |

## 💡 Dica Final

Se você está construindo:
- **Sistema financeiro/contábil:** Use `formatted()` + conversão real de câmbio
- **E-commerce internacional:** Use `formatted()` + API de câmbio
- **Sistema interno/informativo:** Use `formattedWithLocaleCurrency()`
- **Aplicação simples:** Use `formattedWithLocaleCurrency()` e deixe claro que não há conversão
