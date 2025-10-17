@props([
    'id' => '',
    'name' => '',
    'value' => '',
    'wireModel' => '',
    'placeholder' => '',
    'required' => false,
    'currencySymbol' => null,
])

@php
    // Obtém o símbolo da moeda baseado no locale atual
    $locale = app()->getLocale();
    $localeCurrency = config('currency.locale_currency_map')[$locale] ?? 'BRL';
    
    $symbols = [
        'BRL' => 'R$',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY' => '¥',
    ];
    
    $symbol = $currencySymbol ?? ($symbols[$localeCurrency] ?? 'R$');
    
    // Determina o formato do locale
    $isPortuguese = str_starts_with($locale, 'pt');
    $decimalSeparator = $isPortuguese ? ',' : '.';
    $thousandsSeparator = $isPortuguese ? '.' : ',';
@endphp

<div 
    class="relative w-full"
    x-data="{
        value: @entangle($wireModel).live,
        displayValue: '',
        isPortuguese: {{ $isPortuguese ? 'true' : 'false' }},
        
        init() {
            this.displayValue = this.formatMoney(this.value || '');
            
            // Observa mudanças no wire model (quando vem do backend)
            this.\$watch('value', (newVal) => {
                if (document.activeElement !== this.\$refs.input) {
                    this.displayValue = this.formatMoney(newVal || '');
                }
            });
        },
        
        formatMoney(value) {
            if (!value) return '';
            
            // Se já estiver formatado, desformata primeiro
            let cleanValue = this.unformatMoney(value);
            if (!cleanValue) return '';
            
            // Converte para número
            let number = parseFloat(cleanValue);
            if (isNaN(number)) return '';
            
            if (this.isPortuguese) {
                // Formato brasileiro: 1.234,56
                return number.toLocaleString('pt-BR', { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                });
            } else {
                // Formato internacional: 1,234.56
                return number.toLocaleString('en-US', { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                });
            }
        },
        
        unformatMoney(value) {
            if (!value) return '';
            
            // Remove tudo exceto números, ponto e vírgula
            let cleanValue = String(value).replace(/[^\d.,]/g, '');
            
            if (this.isPortuguese) {
                // Remove pontos de milhar e substitui vírgula por ponto
                return cleanValue.replace(/\./g, '').replace(',', '.');
            } else {
                // Remove vírgulas de milhar
                return cleanValue.replace(/,/g, '');
            }
        },
        
        handleInput(event) {
            let input = event.target.value;
            
            // Remove tudo que não é número
            let numbersOnly = input.replace(/\D/g, '');
            
            if (!numbersOnly) {
                this.displayValue = '';
                this.value = '';
                return;
            }
            
            // Trata como centavos
            let cents = parseInt(numbersOnly);
            let decimal = (cents / 100).toFixed(2);
            
            // Atualiza o valor Livewire (sempre em formato decimal)
            this.value = decimal;
            
            // Atualiza o display formatado
            this.displayValue = this.formatMoney(decimal);
        },
        
        handleBlur() {
            // Garante formatação ao sair do campo
            this.displayValue = this.formatMoney(this.value || '');
        }
    }"
>
    <!-- Símbolo da moeda -->
    <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none z-10">
        <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $symbol }}</span>
    </div>
    
    <!-- Input com máscara monetária -->
    <input 
        type="text" 
        id="{{ $id }}"
        name="{{ $name }}"
        x-ref="input"
        x-model="displayValue"
        @input="handleInput($event)"
        @blur="handleBlur()"
        {{ $attributes->merge(['class' => 'block p-2.5 w-full ps-12 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 shadow-sm']) }}
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        autocomplete="off"
    />
</div>
