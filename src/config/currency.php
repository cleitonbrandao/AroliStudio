<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Moeda Padrão
    |--------------------------------------------------------------------------
    |
    | Define a moeda padrão da aplicação
    |
    */

    'default' => env('DEFAULT_CURRENCY', 'BRL'),

    /*
    |--------------------------------------------------------------------------
    | Mapeamento de Locale para Moeda
    |--------------------------------------------------------------------------
    |
    | Define qual moeda usar para cada locale
    |
    */

    'locale_currency_map' => [
        'pt_BR' => 'BRL',
        'en' => 'USD',
        'es' => 'EUR',
        'de' => 'EUR',
    ],

    /*
    |--------------------------------------------------------------------------
    | Moedas Disponíveis
    |--------------------------------------------------------------------------
    |
    | Lista de moedas suportadas pela aplicação
    |
    */

    'available' => [
        'BRL' => [
            'name' => 'Real Brasileiro',
            'symbol' => 'R$',
            'locale' => 'pt_BR',
        ],
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'locale' => 'en_US',
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'locale' => 'de_DE',
        ],
    ],

];
