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
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 z-50 mt-1 w-64 origin-top-right rounded-md bg-yellow-50 dark:bg-yellow-900 shadow-md border border-yellow-200 dark:border-yellow-700 p-3"
            style="display: none;"
        >
            <p class="text-xs text-yellow-800 dark:text-yellow-200">
                {{ __('app.locale_change_restricted') }}
            </p>
        </div>
    @endif
</div>
