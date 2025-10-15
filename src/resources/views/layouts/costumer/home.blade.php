<x-layouts.home 
    title="Clientes"
    :navLinks="[
        ['text' => 'Lista', 'route' => 'root.costumer.index', 'active' => 'root.costumer.index'],
        ['text' => 'Cadastrar', 'route' => 'root.form.costumer', 'active' => 'root.form.costumer']
    ]"
>
    {{ $slot ?? '' }}
</x-layouts.home>
