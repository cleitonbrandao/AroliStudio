<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageCurrencySwitcher extends Component
{
    public string $currentLocale;
    public string $currentCurrency;
    
    public array $availableLocales = [
        'pt_BR' => [
            'name' => 'Português (BR)',
            'flag' => '🇧🇷',
            'currency' => 'BRL'
        ],
        'en' => [
            'name' => 'English',
            'flag' => '🇺🇸',
            'currency' => 'USD'
        ],
    ];

    public function mount()
    {
        $this->currentLocale = App::getLocale();
        $this->currentCurrency = Session::get('currency', config('currency.default'));
    }

    public function switchLocale(string $locale)
    {
        // Valida se o locale é suportado
        if (!array_key_exists($locale, $this->availableLocales)) {
            return;
        }

        // Define o locale na sessão
        Session::put('locale', $locale);
        
        // Define a moeda correspondente
        $currency = $this->availableLocales[$locale]['currency'];
        Session::put('currency', $currency);
        
        // Atualiza as propriedades do componente
        $this->currentLocale = $locale;
        $this->currentCurrency = $currency;
        
        // Emite evento para recarregar a página
        $this->dispatch('locale-changed');
    }

    public function render()
    {
        return view('livewire.components.language-currency-switcher');
    }
}
