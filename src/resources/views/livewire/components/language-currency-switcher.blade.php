<div 
    class="relative inline-block text-left" 
    x-data="{ open: false }"
    @locale-changed.window="window.location.reload()"
>
    <!-- Botão Minimalista -->
    <button 
        @click="open = !open" 
        type="button" 
        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
        title="Idioma e Moeda"
    >
        <span class="text-base">{{ $availableLocales[$currentLocale]['flag'] ?? '🌐' }}</span>
        <span class="uppercase font-semibold tracking-wide">{{ $currentCurrency }}</span>
        
        <!-- Small arrow icon -->
        <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown Menu Compacto -->
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
                <button 
                    wire:click="switchLocale('{{ $locale }}')"
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
            @endforeach
        </div>
    </div>
</div>
