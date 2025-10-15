<x-layouts.home 
    title="Comercial"
    :navLinks="[
        ['text' => 'Resumo de Caixa', 'route' => 'root.commercial.summary', 'active' => 'root.commercial.summary'],
        ['text' => 'Entradas de Consumo', 'route' => 'root.commercial.consumption', 'active' => 'root.commercial.consumption'],
        ['text' => 'Empresas / Fornecedores', 'route' => '#', 'active' => 'root.commercial.consumption']
    ]"
>
    {{ $slot ?? '' }}
</x-layouts.home>
