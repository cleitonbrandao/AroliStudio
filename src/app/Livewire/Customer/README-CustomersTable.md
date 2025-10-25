# CustomersTable Component

Componente Livewire para listagem avançada de clientes com filtros, ordenação e ações inline.

## 📋 Características

### ✨ Funcionalidades Principais

- **Listagem Completa**: Exibe todos os clientes com informações detalhadas
- **Busca em Tempo Real**: Busca por nome, email ou CPF com debounce de 300ms
- **Filtros Avançados**:
  - Todos os clientes
  - Cadastro completo (com CPF e email)
  - Cadastro incompleto (faltando CPF ou email)
- **Ordenação**: Clique nas colunas para ordenar (nome, email, data de cadastro)
- **Paginação**: 15 itens por página com links de navegação
- **Ações Inline**:
  - Editar cliente
  - Excluir cliente (com modal de confirmação)
- **Estatísticas em Cards**:
  - Total de clientes
  - Clientes com CPF
  - Clientes com email
  - Percentual de cadastros completos
- **Indicadores de Status**:
  - Badge verde: Cadastro completo
  - Badge amarelo: Cadastro incompleto
- **Dark Mode**: Suporte completo ao modo escuro
- **Responsivo**: Layout adaptável para mobile, tablet e desktop

### 🎨 Interface

#### Cards de Estatísticas
- Total de Clientes (ícone azul)
- Com CPF (ícone verde)
- Com Email (ícone roxo)
- Cadastro Completo % (ícone índigo)

#### Filtros
- Campo de busca com ícone de lupa
- Select de status (Todos, Completo, Incompleto)
- Botão "Limpar" para resetar filtros

#### Tabela
| Coluna | Ordenável | Descrição |
|--------|-----------|-----------|
| Cliente | ✅ | Avatar + Nome completo |
| Email | ✅ | Endereço de email |
| Telefone | ❌ | Telefone formatado |
| CPF | ❌ | CPF formatado (000.000.000-00) |
| Status | ❌ | Badge Completo/Incompleto |
| Cadastro | ✅ | Data e hora do cadastro |
| Ações | ❌ | Botões Editar/Excluir |

## 📁 Arquivos

```
app/Livewire/Customer/CustomersTable.php
resources/views/livewire/customer/customers-table.blade.php
```

## 🛠️ Propriedades Públicas

```php
// Busca
#[Url(as: 'q')]
public string $search = '';

// Filtros
#[Url(as: 'status')]
public string $filterStatus = 'all'; // all, active, incomplete

// Ordenação
#[Url(as: 'sort')]
public string $sortField = 'created_at';

#[Url(as: 'dir')]
public string $sortDirection = 'desc';

// Paginação
public int $perPage = 15;

// Modal
public bool $showDeleteModal = false;
public ?int $customerToDelete = null;
```

## 🔧 Métodos Principais

### Navegação
```php
create()           // Redireciona para formulário de criação
edit($id)          // Redireciona para formulário de edição
```

### Filtros e Busca
```php
updatedSearch()         // Reseta paginação ao buscar
updatedFilterStatus()   // Reseta paginação ao filtrar
sortBy($field)          // Alterna ordenação da coluna
clearFilters()          // Limpa todos os filtros
```

### Exclusão
```php
confirmDelete($id)      // Abre modal de confirmação
cancelDelete()          // Fecha modal
delete()                // Executa exclusão
```

## 🚀 Uso

### Como Componente Principal
```php
// routes/web.php
Route::get('/customers', CustomersTable::class)->name('customers.table');
```

### Como Componente Inline
```blade
<livewire:customer.customers-table />
```

### Como Lazy Component
```blade
<livewire:customer.customers-table lazy />
```

## 🎯 Parâmetros de URL

O componente usa `#[Url]` attributes para sincronizar o estado com a URL:

```
/customers?q=joão&status=active&sort=email&dir=asc
```

- `q`: Termo de busca
- `status`: Filtro de status (all, active, incomplete)
- `sort`: Campo de ordenação
- `dir`: Direção (asc, desc)

## 📊 Estatísticas

O componente carrega estatísticas via `CustomerService::getStatistics()`:

```php
[
    'total' => 150,              // Total de clientes
    'with_cpf' => 120,          // Clientes com CPF
    'with_email' => 145,        // Clientes com email
    'complete_percentage' => 80.0 // % de cadastros completos
]
```

## 🔍 Scope de Busca

O componente usa o scope `search()` do model Customer:

```php
Customer::search($term) // Busca em: name, email, cpf
```

## 🛡️ Segurança

- **Multi-tenancy**: Usa `auth()` scope automático
- **Team Isolation**: Queries filtradas por `team_id`
- **Authorization**: Middleware `user.has.team`
- **CSRF Protection**: Livewire automaticamente protege

## 🎨 Personalização

### Alterar Itens por Página
```php
public int $perPage = 25; // Padrão: 15
```

### Alterar Ordenação Padrão
```php
public string $sortField = 'email';      // Padrão: created_at
public string $sortDirection = 'asc';    // Padrão: desc
```

### Customizar Filtros
Adicione novos filtros no método `render()`:

```php
if ($this->filterStatus === 'new_status') {
    $query->where('custom_condition', true);
}
```

## 🚦 Estados da UI

### Empty State
- **Sem filtros**: Mostra mensagem + botão "Novo Cliente"
- **Com filtros**: Sugere ajustar os filtros

### Loading States
- Busca com debounce de 300ms
- Modal de exclusão com loading
- Wire:loading nos botões de ação

### Feedback States
- Mensagem de sucesso (verde)
- Mensagem de erro (vermelho)
- Flash messages persistentes

## 🔄 Fluxo de Exclusão

1. Usuário clica em "Excluir" na tabela
2. `confirmDelete($id)` abre modal
3. Modal exibe aviso de confirmação
4. Usuário confirma ou cancela
5. `delete()` executa via `CustomerService`
6. Flash message de sucesso/erro
7. Modal fecha e tabela atualiza

## 📱 Responsividade

### Mobile (<640px)
- Cards de estatísticas empilhados
- Tabela com scroll horizontal
- Botões full-width

### Tablet (640px - 1024px)
- 2 cards por linha
- Tabela responsiva

### Desktop (>1024px)
- 4 cards por linha
- Tabela completa visível

## 🎭 Dark Mode

Totalmente suportado com classes Tailwind:
- `dark:bg-gray-800`
- `dark:text-white`
- `dark:border-gray-700`

## ⚡ Performance

- **Lazy Loading**: Usa `wire:loading` para feedback
- **Debounce**: 300ms na busca
- **Pagination**: 15 itens por página
- **Eager Loading**: `with('people', 'team')`
- **URL State**: Mantém estado ao recarregar

## 🐛 Debug

### Verificar Queries
```php
DB::enableQueryLog();
// Renderizar componente
dd(DB::getQueryLog());
```

### Verificar State
```blade
<div>{{ json_encode($this->all()) }}</div>
```

## 📚 Dependências

- **Livewire 3.x**: Framework reativo
- **Tailwind CSS**: Estilos
- **CustomerService**: Lógica de negócio
- **Customer Model**: Com scopes `auth()` e `search()`

## 🔗 Rotas Relacionadas

```php
customers.index   // GET /customers
customers.create  // GET /customers/create
customers.edit    // GET /customers/{id}/edit
```

## 📝 Notas

- Usa `navigate: true` para SPA-like navigation
- Compatível com Livewire Wire Spa
- Suporta Alpine.js para máscaras
- Integrado com sistema de auditoria
