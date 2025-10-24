# 🏢 Implementação: Locale por Team (Empresa)

## 📊 Análise da Solução

### ✅ Vantagens de Locale no Team

1. **Consistência:** Todos os usuários do team veem os mesmos formatos
2. **Controle:** Apenas gerentes podem alterar (permissão centralizada)
3. **Persistência:** Não depende de sessão individual
4. **Escalabilidade:** Cada empresa/team tem sua própria configuração
5. **Sem conflito:** Não interfere com flash messages de formulários

### 🎯 Requisitos

- ✅ Adicionar coluna `locale` na tabela `teams`
- ✅ Apenas Owner e Manager podem alterar
- ✅ SetLocale middleware busca do `currentTeam`
- ✅ LocaleController verifica permissão antes de mudar
- ✅ Fallback para locale padrão se team não tiver definido
- ✅ Manter compatibilidade com estrutura atual

---

## 🔧 Implementação

### 1. Migration: Adicionar Coluna `locale` em Teams

**Arquivo:** `database/migrations/YYYY_MM_DD_add_locale_to_teams_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('locale', 10)
                  ->default('pt_BR')
                  ->after('personal_team')
                  ->comment('Locale/idioma do team (pt_BR, en, es, de)');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
```

---

### 2. Model: Atualizar Team Model

**Arquivo:** `app/Models/Team.php`

```php
protected $fillable = [
    'name',
    'slug',
    'personal_team',
    'locale',  // ← ADICIONAR
];

protected $casts = [
    'personal_team' => 'boolean',
];

/**
 * Get the locale for this team.
 * Falls back to app default if not set.
 */
public function getLocaleAttribute($value): string
{
    return $value ?? config('app.locale', 'pt_BR');
}
```

---

### 3. SetLocale Middleware: Buscar do Team

**Arquivo:** `app/Http/Middleware/SetLocale.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Obtém o locale do currentTeam do usuário autenticado
        $locale = $this->resolveLocale($request);
        
        // Log para debug
        Log::debug('SetLocale middleware', [
            'user_id' => Auth::id(),
            'team_id' => Auth::user()?->currentTeam?->id,
            'team_locale' => Auth::user()?->currentTeam?->locale,
            'final_locale' => $locale,
            'route' => $request->path(),
        ]);
        
        // Define o locale da aplicação
        App::setLocale($locale);
        
        // Define a moeda baseada no locale
        $currency = config("currency.locale_currency_map.{$locale}", config('currency.default'));
        
        // Disponibiliza para todas as views
        view()->share('currentCurrency', $currency);
        view()->share('currentLocale', $locale);
        
        return $next($request);
    }

    /**
     * Resolve o locale a ser usado.
     * Prioridade: Team → Config Padrão
     */
    private function resolveLocale(Request $request): string
    {
        // Se usuário está autenticado e tem currentTeam
        if (Auth::check() && Auth::user()->currentTeam) {
            return Auth::user()->currentTeam->locale ?? config('app.locale');
        }
        
        // Fallback para locale padrão da aplicação
        return config('app.locale');
    }
}
```

---

### 4. LocaleController: Verificar Permissão

**Arquivo:** `app/Http/Controllers/LocaleController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LocaleController extends Controller
{
    private const VALID_LOCALES = ['pt_BR', 'en', 'es', 'de'];

    /**
     * Muda o locale do Team.
     * Apenas Owner e usuários com role 'manager' podem alterar.
     */
    public function change(Request $request)
    {
        $user = Auth::user();
        $team = $user->currentTeam;
        
        // Verifica se usuário tem team
        if (!$team) {
            return redirect()->back()->with('error', __('app.no_team_selected'));
        }
        
        // Verifica permissão: Owner ou Manager
        if (!$this->canChangeLocale($user, $team)) {
            return redirect()->back()->with('error', __('app.no_permission_change_locale'));
        }
        
        $locale = $request->input('locale', config('app.locale'));
        
        // Log
        Log::info('LocaleController: Mudando locale do team', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'from' => $team->locale,
            'to' => $locale,
        ]);
        
        // Valida locale
        if (!in_array($locale, self::VALID_LOCALES)) {
            Log::warning('LocaleController: Locale inválido', ['locale' => $locale]);
            return redirect()->back()->with('error', __('app.invalid_locale'));
        }
        
        // Atualiza o locale do team
        $team->update(['locale' => $locale]);
        
        // Log de confirmação
        Log::info('LocaleController: Locale do team atualizado', [
            'team_id' => $team->id,
            'locale' => $locale,
            'currency' => config("currency.locale_currency_map.{$locale}"),
        ]);
        
        $currency = config("currency.locale_currency_map.{$locale}", config('currency.default'));
        
        return redirect()->back()->with([
            'locale_changed' => __('app.locale_changed'),
            'locale' => $locale,
            'currency' => $currency,
        ]);
    }

    /**
     * Verifica se o usuário pode alterar o locale do team.
     * Regra: Apenas Owner ou Manager podem alterar.
     */
    private function canChangeLocale($user, $team): bool
    {
        // Owner sempre pode
        if ($team->user_id === $user->id) {
            return true;
        }
        
        // Verifica se tem role 'manager' no team
        $membership = $team->users()
            ->where('user_id', $user->id)
            ->first();
        
        if ($membership && $membership->pivot->role === 'manager') {
            return true;
        }
        
        return false;
    }

    /**
     * Retorna o locale atual do team.
     */
    public function current()
    {
        $user = Auth::user();
        $team = $user->currentTeam;
        
        return response()->json([
            'locale' => $team ? $team->locale : config('app.locale'),
            'currency' => config("currency.locale_currency_map.{$team->locale}", config('currency.default')),
            'available_locales' => self::VALID_LOCALES,
            'can_change' => $team ? $this->canChangeLocale($user, $team) : false,
        ]);
    }
}
```

---

### 5. Locale Switcher: Mostrar Apenas para Autorizados

**Arquivo:** `resources/views/components/locale-switcher.blade.php`

```php
@php
    use Illuminate\Support\Facades\Auth;
    
    $user = Auth::user();
    $team = $user?->currentTeam;
    $currentLocale = $team?->locale ?? app()->getLocale();
    $currentCurrency = config('currency.locale_currency_map')[$currentLocale] ?? 'BRL';
    
    // Verifica se pode alterar
    $canChange = false;
    if ($team) {
        // Owner sempre pode
        $canChange = ($team->user_id === $user->id);
        
        // Manager também pode
        if (!$canChange) {
            $membership = $team->users()->where('user_id', $user->id)->first();
            $canChange = ($membership && $membership->pivot->role === 'manager');
        }
    }
    
    $availableLocales = [
        'pt_BR' => ['flag' => '🇧🇷', 'name' => __('app.locale_pt_BR') ?? 'Português (Brasil)', 'currency' => 'BRL'],
        'en' => ['flag' => '🇺🇸', 'name' => __('app.locale_en') ?? 'English (US)', 'currency' => 'USD'],
        'es' => ['flag' => '🇪🇸', 'name' => __('app.locale_es') ?? 'Español', 'currency' => 'EUR'],
        'de' => ['flag' => '🇩🇪', 'name' => __('app.locale_de') ?? 'Deutsch', 'currency' => 'EUR'],
    ];
@endphp

<div class="relative inline-block text-left ms-3" x-data="{ open: false }">
    <!-- Botão (sempre visível) -->
    <button 
        @click="open = !open" 
        type="button" 
        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
        title="{{ $canChange ? __('app.language_currency') : __('app.current_language') }}"
    >
        <span class="text-base">{{ $availableLocales[$currentLocale]['flag'] ?? '🌐' }}</span>
        <span class="uppercase font-semibold tracking-wide">{{ $currentCurrency }}</span>
        
        @if($canChange)
            <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
            </svg>
        @endif
    </button>

    <!-- Dropdown (apenas se pode alterar) -->
    @if($canChange)
        <div 
            x-show="open" 
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 z-50 mt-1 w-48 origin-top-right rounded-md bg-white shadow-md border border-gray-200 dark:bg-gray-800 dark:border-gray-700"
            style="display: none;"
        >
            <div class="py-1">
                @foreach($availableLocales as $locale => $data)
                    <form action="{{ route('locale.change') }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="locale" value="{{ $locale }}">
                        <button 
                            type="submit"
                            class="flex items-center w-full px-3 py-1.5 text-xs hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all {{ $currentLocale === $locale ? 'bg-gray-50 dark:bg-gray-700/30' : '' }}"
                        >
                            <span class="mr-2 text-sm">{{ $data['flag'] }}</span>
                            <span class="flex-1 text-left font-medium text-gray-700 dark:text-gray-300">{{ $data['name'] }}</span>
                            <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ $data['currency'] }}</span>
                            
                            @if($currentLocale === $locale)
                                <svg class="w-3 h-3 ml-1.5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @else
        {{-- Tooltip explicando que apenas gerentes podem alterar --}}
        <div 
            x-show="open" 
            @click.away="open = false"
            class="absolute right-0 z-50 mt-1 w-64 origin-top-right rounded-md bg-yellow-50 dark:bg-yellow-900 shadow-md border border-yellow-200 dark:border-yellow-700 p-3"
            style="display: none;"
        >
            <p class="text-xs text-yellow-800 dark:text-yellow-200">
                {{ __('app.locale_change_restricted') }}
            </p>
        </div>
    @endif
</div>
```

---

### 6. Traduções

**Arquivo:** `resources/lang/pt_BR/app.php`

```php
'no_team_selected' => 'Nenhum time selecionado.',
'no_permission_change_locale' => 'Apenas gerentes e proprietários podem alterar o idioma.',
'locale_change_restricted' => 'Apenas gerentes e proprietários do time podem alterar o idioma.',
'current_language' => 'Idioma atual',
```

**Arquivo:** `resources/lang/en/app.php`

```php
'no_team_selected' => 'No team selected.',
'no_permission_change_locale' => 'Only managers and owners can change the language.',
'locale_change_restricted' => 'Only team managers and owners can change the language.',
'current_language' => 'Current language',
```

---

### 7. Toast de Sucesso (Global)

**Arquivo:** `resources/views/navigation-menu.blade.php`

Adicionar logo após o `</nav>`:

```blade
{{-- Toast para mudança de locale --}}
@if (session('locale_changed'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-20 right-4 z-50 p-4 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-lg shadow-lg border border-indigo-200 dark:border-indigo-700 max-w-sm"
    >
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">{{ session('locale_changed') }}</span>
        </div>
    </div>
@endif
```

---

## 📋 Checklist de Implementação

- [ ] 1. Criar migration `add_locale_to_teams_table`
- [ ] 2. Executar `php artisan migrate`
- [ ] 3. Atualizar `Team.php` model (fillable + accessor)
- [ ] 4. Atualizar `SetLocale.php` middleware
- [ ] 5. Atualizar `LocaleController.php` (permissões)
- [ ] 6. Atualizar `locale-switcher.blade.php` (verificação de permissão)
- [ ] 7. Adicionar traduções em `pt_BR/app.php` e `en/app.php`
- [ ] 8. Adicionar toast em `navigation-menu.blade.php`
- [ ] 9. Limpar caches: `php artisan config:clear && php artisan view:clear`
- [ ] 10. Testar como Owner (deve poder alterar)
- [ ] 11. Testar como Manager (deve poder alterar)
- [ ] 12. Testar como Employee (NÃO deve poder alterar)
- [ ] 13. Verificar persistência ao recarregar
- [ ] 14. Verificar que todos do team veem o mesmo locale

---

## 🔄 Fluxo Completo

```
┌─────────────────────┐
│   Usuário Login     │
│   currentTeam set   │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────────┐
│  SetLocale Middleware   │
│  Auth::user()->         │
│    currentTeam->locale  │
│  App::setLocale()       │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│  Interface carrega      │
│  Locale: pt_BR          │
│  Moeda: R$ 1.234,57     │
└─────────────────────────┘

Se MANAGER/OWNER clica no seletor:
           │
           ▼
┌─────────────────────────┐
│  LocaleController       │
│  verifica permissão     │
│  team->update(locale)   │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│  Próxima request        │
│  SetLocale lê novo      │
│  locale do team         │
│  TODOS veem mudança     │
└─────────────────────────┘
```

---

## ✅ Vantagens desta Abordagem

| Aspecto | Solução Antiga (Sessão) | Nova Solução (Team) |
|---------|-------------------------|---------------------|
| **Persistência** | ⚠️ 2 horas (sessão) | ✅ Permanente (DB) |
| **Consistência** | ❌ Cada usuário diferente | ✅ Todo team igual |
| **Controle** | ❌ Qualquer um muda | ✅ Apenas gerentes |
| **Flash Messages** | ❌ Conflito com Livewire | ✅ Sem conflito |
| **Multi-tenant** | ❌ Não suporta | ✅ Cada team seu locale |
| **Escalabilidade** | ⚠️ Depende de sessões | ✅ Banco de dados |

---

## 🧪 Testes

### Teste 1: Owner Pode Alterar
```php
// Como owner do team
1. Login como owner
2. Clicar no seletor de idioma
3. Ver dropdown com opções
4. Selecionar "English"
5. ✅ Ver toast "Idioma alterado com sucesso"
6. ✅ Interface muda para inglês
7. Recarregar página
8. ✅ Continua em inglês
```

### Teste 2: Manager Pode Alterar
```php
// Como manager do team
1. Login como usuário com role 'manager'
2. Mesmos passos acima
3. ✅ Deve funcionar
```

### Teste 3: Employee NÃO Pode Alterar
```php
// Como employee do team
1. Login como usuário comum (sem role manager)
2. Ver seletor (só leitura, sem seta)
3. Clicar: ver tooltip explicativo
4. ❌ NÃO consegue abrir dropdown
```

### Teste 4: Todos Veem a Mudança
```php
// Owner muda para inglês
1. Owner muda locale para 'en'
2. Todos os usuários do team (incluindo employees)
3. ✅ Veem interface em inglês
4. ✅ Valores formatados como USD
```

---

## 🚀 Migração de Dados Existentes

Se já existem teams sem locale definido:

```php
// database/seeders/SetDefaultTeamLocaleSeeder.php
php artisan make:seeder SetDefaultTeamLocaleSeeder

class SetDefaultTeamLocaleSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Team::whereNull('locale')
            ->update(['locale' => 'pt_BR']);
            
        $this->command->info('Teams atualizados com locale padrão pt_BR');
    }
}

// Executar:
php artisan db:seed --class=SetDefaultTeamLocaleSeeder
```

---

## 📝 Notas Importantes

1. **Compatibilidade:** Não quebra nada existente (middleware já existe)
2. **Fallback:** Se team não tiver locale, usa config default
3. **Performance:** 1 query extra por request (eager load com currentTeam)
4. **Segurança:** Validação de permissão antes de alterar
5. **UX:** Usuários sem permissão veem apenas o locale atual
