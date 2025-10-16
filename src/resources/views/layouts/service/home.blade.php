<x-layouts.home 
    title="Serviços"
    :navLinks="[
        ['text' => 'Lista', 'route' => 'root.negotiable', 'active' => 'root.negotiable'],
        ['text' => 'Cadastrar - Produtos', 'route' => 'root.form.product', 'active' => 'root.form.product'],
        ['text' => 'Cadastrar - Serviços', 'route' => 'root.form.service', 'active' => 'root.form.service'],
        ['text' => 'Cadastrar - Pacotes', 'route' => 'root.form.package', 'active' => 'root.form.package']
    ]"
>
    {{ $slot ?? '' }}
</x-layouts.home>