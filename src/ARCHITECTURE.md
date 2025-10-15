# 🏗️ Arquitetura do AroliStudio

## 📋 Visão Geral

O AroliStudio é uma plataforma SaaS para gestão de empresas e equipes, construída com Laravel 11, Jetstream, Livewire e Docker.

## 🎯 Fluxo Principal

```
1. USUÁRIO SE CADASTRA
   ↓
2. USUÁRIO PODE CRIAR EMPRESAS (múltiplas)
   ↓
3. PARA CADA EMPRESA: CONVIDAR MEMBROS
   ↓
4. BILLING BASEADO EM:
   - Número de empresas
   - Número de usuários ativos
```

**Nota:** Removemos o team pessoal automático. O usuário só cria empresas quando necessário.

## 🏢 Estrutura de Dados

### Tabelas Principais

#### `users`
- Dados do usuário
- Informações de billing (Stripe)
- Trial period

#### `teams` (Companies)
- Empresas criadas pelos usuários
- Limites de usuários
- Status ativo/inativo
- Trial period

#### `team_user` (Memberships)
- Relacionamento usuário-empresa
- Roles (owner, admin, member)

#### `subscriptions`
- Assinaturas do usuário
- Dados do Stripe
- Status e datas

#### `company_subscriptions`
- Assinaturas específicas por empresa
- Limites de usuários/empresas
- Planos (free, basic, premium, enterprise)

## 🔐 Sistema de Permissões

### Roles
- **Owner**: Criador da empresa, acesso total
- **Admin**: Pode gerenciar membros e configurações
- **Member**: Acesso básico, apenas leitura

### Permissões por Role
```php
'owner' => ['create', 'read', 'update', 'delete']
'admin' => ['create', 'read', 'update']
'member' => ['read']
```

### 🏢 Sistema de Franquias e Filiais

**Cenário Real:**
- **João** é Owner da "Restaurante Matriz Ltda"
- **Maria** é Owner da "Restaurante Filial Centro" (franqueada)
- **Maria** é Member na matriz (para relatórios)
- **Ana** é Admin na matriz e Admin nas filiais (gerente regional)

**Estrutura de Dados:**
```php
// João (Owner da Matriz)
User: João Silva
├── Restaurante Matriz Ltda (role: owner)
└── Permissões: ['create', 'read', 'update', 'delete']

// Maria (Franqueada)
User: Maria Santos
├── Restaurante Filial Centro (role: owner)
├── Restaurante Matriz Ltda (role: member)
└── Permissões Filial: ['create', 'read', 'update', 'delete']
└── Permissões Matriz: ['read']

// Ana (Gerente Regional)
User: Ana Costa
├── Restaurante Matriz Ltda (role: admin)
├── Restaurante Filial Centro (role: admin)
└── Permissões: ['create', 'read', 'update']
```

## 💳 Sistema de Assinaturas

### Planos Disponíveis

#### Free (Gratuito)
- 1 empresa
- 5 usuários por empresa
- Suporte por email

#### Basic (R$ 29,90/mês)
- 3 empresas
- 25 usuários por empresa
- Suporte prioritário
- Relatórios avançados

#### Premium (R$ 79,90/mês)
- 10 empresas
- 100 usuários por empresa
- Suporte 24/7
- API completa
- Integrações avançadas

#### Enterprise (R$ 199,90/mês)
- Empresas ilimitadas
- Usuários ilimitados
- Suporte dedicado
- Customizações
- SLA garantido

### Limites e Verificações

#### Middleware de Limites
```php
// Verificar se pode criar empresa
Route::middleware(['auth', 'subscription:create_company'])

// Verificar se pode adicionar usuário
Route::middleware(['auth', 'subscription:add_user'])
```

#### Serviços
- `SubscriptionService`: Gerencia assinaturas e limites
- `BillingService`: Integração com Stripe (futuro)

## 🚀 Fluxo de Desenvolvimento

### 1. Cadastro de Usuário
```php
// Usuário se cadastra (sem team pessoal)
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);

// Usuário pode criar empresas quando quiser
// Não há team pessoal automático
```

### 2. Criação de Empresa
```php
// Verificar limites antes de criar
$limits = $subscriptionService->canUserCreateCompany($user);
if (!$limits['can_create']) {
    return redirect()->route('subscription.upgrade');
}

// Criar empresa
$company = Team::create([
    'name' => $request->name,
    'user_id' => $user->id,
    'personal_team' => false,
    'max_users' => 5,
    'current_users' => 1,
    'plan_type' => 'free',
]);
```

### 3. Convite de Membros
```php
// Verificar limites antes de adicionar
$limits = $subscriptionService->canCompanyAddUser($company);
if (!$limits['can_add']) {
    return redirect()->back()->with('error', 'Limite atingido');
}

// Adicionar membro
$company->addUserWithCount($newUser, 'member');
```

## 🔧 Componentes Técnicos

### Models Principais
- `User`: Usuário com billing e assinaturas
- `Team/Company`: Empresa com limites e assinaturas
- `Membership`: Relacionamento usuário-empresa
- `Subscription`: Assinatura do usuário
- `CompanySubscription`: Assinatura específica da empresa

### Services
- `SubscriptionService`: Gerencia assinaturas e limites
- `BillingService`: Integração com Stripe (futuro)
- `InvitationService`: Gerencia convites (futuro)

### Middleware
- `CheckSubscriptionLimits`: Verifica limites antes de ações
- `EnsureCompanyActive`: Verifica se empresa está ativa

### Livewire Components
- `Companies/Index`: Lista empresas do usuário
- `Companies/Create`: Criação de empresa
- `Companies/Invite`: Convite de membros
- `Subscription/Upgrade`: Upgrade de plano

## 📊 Monitoramento e Analytics

### Métricas Importantes
- Usuários ativos por empresa
- Empresas criadas por usuário
- Conversão de trial para pago
- Churn rate por plano

### Logs e Auditoria
- Todas as ações de billing
- Criação/remoção de usuários
- Mudanças de plano
- Convites enviados/aceitos

## 🔮 Roadmap Futuro

### Fase 1 (Atual)
- ✅ Sistema básico de usuários e empresas
- ✅ Limites por plano
- ✅ Middleware de verificação

### Fase 2 (Próxima)
- 🔄 Integração com Stripe
- 🔄 Sistema de convites por email
- 🔄 Dashboard de analytics

### Fase 3 (Futuro)
- 📋 API completa
- 📋 Integrações com ferramentas externas
- 📋 Sistema de notificações
- 📋 Relatórios avançados

## 🛠️ Comandos Úteis

### Desenvolvimento
```bash
# Iniciar projeto
./start-laravel11.ps1

# Compilar assets
docker-compose exec app npm run build

# Executar migrações
docker-compose exec app php artisan migrate

# Criar usuário de teste
docker-compose exec app php artisan tinker
```

### Produção
```bash
# Deploy
docker-compose -f docker-compose.prod.yml up -d

# Backup
docker-compose exec app php artisan backup:run

# Monitoramento
docker-compose logs -f app
```

## 📝 Notas de Implementação

### Boas Práticas Seguidas
- ✅ Separação de responsabilidades
- ✅ Uso de Services para lógica complexa
- ✅ Middleware para verificações
- ✅ Transações para operações críticas
- ✅ Validação de limites antes de ações
- ✅ Grace period para cancelamentos

### Considerações de Performance
- Índices nas tabelas de billing
- Cache de limites de assinatura
- Lazy loading de relacionamentos
- Queue para emails de convite

### Segurança
- Verificação de permissões em todas as ações
- Validação de limites antes de operações
- Logs de auditoria para billing
- Proteção contra CSRF e XSS
