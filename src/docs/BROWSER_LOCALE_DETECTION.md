# 🌍 Detecção Automática de Idioma - Team Invitations

## Visão Geral

A página de convite para times (`/team-invitations/{id}`) detecta automaticamente o idioma preferido do usuário **não autenticado** com base nas configurações do navegador.

## Como Funciona

### 1. **Header Accept-Language**

O navegador envia automaticamente um header HTTP chamado `Accept-Language` com a preferência de idiomas do usuário:

```
Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7
```

**Formato:**
- `pt-BR`: Português do Brasil (prioridade máxima: q=1.0)
- `pt;q=0.9`: Português genérico (prioridade 0.9)
- `en-US;q=0.8`: Inglês americano (prioridade 0.8)
- `en;q=0.7`: Inglês genérico (prioridade 0.7)

### 2. **Processamento no Controller**

O `TeamInvitationController` processa o header em três etapas:

#### **Etapa 1: Parse do Header**
```php
private function parseAcceptLanguage(string $acceptLanguage): array
```
- Remove espaços e divide por vírgula
- Extrai o código do idioma e o quality factor (q)
- Ordena por prioridade (maior q primeiro)
- Retorna array ordenado: `['pt-BR', 'pt', 'en-US', 'en']`

#### **Etapa 2: Mapeamento**
```php
$localeMap = [
    'pt-br' => 'pt_BR',
    'pt' => 'pt_BR',
    'en-us' => 'en',
    'en-gb' => 'en',
    'en' => 'en',
    'es-es' => 'es',
    'es' => 'es',
];
```

Converte códigos do navegador para os locales da aplicação.

#### **Etapa 3: Validação e Aplicação**
```php
$supportedLocales = ['pt_BR', 'en', 'es'];
```

Valida se o locale é suportado e aplica usando `App::setLocale()`.

### 3. **Fluxo Completo**

```
┌─────────────────────────────────────────────────────────────────┐
│  1. Usuário clica no link de convite (não autenticado)          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  2. Navegador envia Accept-Language: pt-BR,pt;q=0.9,en;q=0.8    │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  3. TeamInvitationController::accept()                          │
│     - Detecta usuário não autenticado                           │
│     - Chama setLocaleFromBrowser($request)                      │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  4. setLocaleFromBrowser()                                      │
│     - Lê header Accept-Language                                 │
│     - Parse: ['pt-BR', 'pt', 'en']                             │
│     - Mapeia: pt-BR → pt_BR                                    │
│     - Valida: pt_BR está em supportedLocales                   │
│     - Aplica: App::setLocale('pt_BR')                          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  5. View renderizada com strings traduzidas                     │
│     - __('You have been invited!') → "Você foi convidado!"     │
│     - __('Log in') → "Fazer Login"                             │
│     - __('Register') → "Criar Conta"                           │
└─────────────────────────────────────────────────────────────────┘
```

## Idiomas Suportados

| Código Navegador | Locale App | Idioma |
|------------------|------------|--------|
| `pt-BR`, `pt` | `pt_BR` | 🇧🇷 Português do Brasil |
| `en-US`, `en-GB`, `en` | `en` | 🇺🇸 English |
| `es-ES`, `es` | `es` | 🇪🇸 Español |

## Fallback

Se nenhum idioma compatível for encontrado:
```php
// Usa o locale padrão definido em config/app.php
App::setLocale(config('app.fallback_locale')); // pt_BR
```

## Logs

O sistema registra a detecção de idioma:

```php
Log::info('Locale definido baseado no navegador', [
    'accept_language' => 'pt-BR,pt;q=0.9,en;q=0.8',
    'detected_language' => 'pt-BR',
    'locale_set' => 'pt_BR',
]);
```

## Exemplos de Detecção

### Exemplo 1: Navegador em Português
```
Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7
```
**Resultado:** `pt_BR` ✅

### Exemplo 2: Navegador em Inglês
```
Accept-Language: en-US,en;q=0.9
```
**Resultado:** `en` ✅

### Exemplo 3: Navegador em Espanhol
```
Accept-Language: es-ES,es;q=0.9,en;q=0.8
```
**Resultado:** `es` ✅

### Exemplo 4: Idioma não suportado
```
Accept-Language: fr-FR,fr;q=0.9
```
**Resultado:** `pt_BR` (fallback) ⚠️

## Configuração do Navegador

### Chrome/Edge
1. Configurações → Idiomas
2. Adicionar idiomas preferidos
3. Ordenar por prioridade (arrastar)

### Firefox
1. Preferências → Idioma
2. Escolher idiomas alternativos
3. Definir ordem de preferência

### Safari
1. Preferências do Sistema → Idioma e Região
2. Idiomas preferidos
3. Safari usa automaticamente

## Testing

### Via cURL
```bash
# Português
curl -H "Accept-Language: pt-BR,pt;q=0.9" http://localhost:8000/team-invitations/4

# Inglês
curl -H "Accept-Language: en-US,en;q=0.9" http://localhost:8000/team-invitations/4

# Espanhol
curl -H "Accept-Language: es-ES,es;q=0.9" http://localhost:8000/team-invitations/4
```

### Via Navegador DevTools
1. F12 → Console
2. ```javascript
   navigator.languages // Ver idiomas configurados
   ```
3. Network → Headers → Request Headers → Accept-Language

## Limitações

1. **Apenas para usuários não autenticados**
   - Usuários logados usam o locale do Team (SetLocale middleware)

2. **Por requisição**
   - O locale não é armazenado
   - Cada requisição detecta novamente

3. **Navegadores privados**
   - Podem enviar headers genéricos
   - Ex: `Accept-Language: en-US`

## Arquivos Relacionados

- **Controller:** `app/Http/Controllers/TeamInvitationController.php`
- **View:** `resources/views/team-invitations/show.blade.php`
- **Traduções:**
  - `lang/pt_BR/team-invitations.php`
  - `lang/en/team-invitations.php`
  - `lang/es/team-invitations.php`

## Benefícios

✅ **Experiência personalizada** - Usuário vê o convite no seu idioma  
✅ **Sem configuração manual** - Automático baseado no navegador  
✅ **Multi-idioma** - Suporte para português, inglês e espanhol  
✅ **Fallback inteligente** - Sempre mostra conteúdo, mesmo sem match  
✅ **Logs detalhados** - Facilita debugging e análise  

## Próximos Passos

- [ ] Adicionar mais idiomas (francês, alemão, italiano)
- [ ] Armazenar preferência de idioma na sessão
- [ ] Dashboard de estatísticas de idiomas mais usados
- [ ] A/B testing de taxas de conversão por idioma
