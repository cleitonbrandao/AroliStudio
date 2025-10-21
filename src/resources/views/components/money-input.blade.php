@props([
    'id' => '',
    'wireModel' => '',
    'placeholder' => '0,00',
    'required' => false,
])

@php
    $locale = app()->getLocale();
    $localeCurrency = config('currency.locale_currency_map')[$locale] ?? 'BRL';
    
    $symbols = [
        'BRL' => 'R$',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY' => '¥',
    ];
    
    $symbol = $symbols[$localeCurrency] ?? 'R$';
@endphp

<div class="relative w-full">
    <!-- Símbolo da moeda -->
    <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none z-10">
        <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $symbol }}</span>
    </div>
    
    <!-- Input com wire:model e máscara via Alpine.js -->
    <input 
        type="text" 
        id="{{ $id }}"
        wire:keydown="{{ $wireModel }}"
        x-data="{ formatted: '' }"
        x-init="
            formatted = $wire.get('{{ $wireModel }}') || '';
            if (formatted) {
                let num = parseFloat(formatted);
                if (!isNaN(num)) {
                    $el.value = num.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            }
        "
        x-on:input="
            let input = $el.value;
            let numbersOnly = input.replace(/\D/g, '');
            if (!numbersOnly) {
                $el.value = '';
                $wire.set('{{ $wireModel }}', '');
                return;
            }
            let cents = parseInt(numbersOnly);
            let decimal = (cents / 100).toFixed(2);
            let num = parseFloat(decimal);
            $el.value = num.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            let cursorPos = $el.value.length;
            $el.setSelectionRange(cursorPos, cursorPos);
            
            $wire.set('{{ $wireModel }}', decimal);
        "
        x-on:blur="
            let val = $wire.get('{{ $wireModel }}');
            if (val) {
                let num = parseFloat(val);
                if (!isNaN(num)) {
                    $el.value = num.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            }
        "
        {{ $attributes->merge(['class' => 'block p-2.5 w-full ps-12 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 shadow-sm']) }}
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        autocomplete="off"
    />
</div>
