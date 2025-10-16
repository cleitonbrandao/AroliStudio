<x-layouts.home 
    title="Funcionários"
    :navLinks="[
        ['text' => 'Lista', 'route' => 'root.employee.index', 'active' => 'root.employee.index'],
        ['text' => 'Cadastrar', 'route' => 'root.form.employee', 'active' => 'root.form.employee']
    ]"
>
    {{ $slot }}
</x-layouts.home>
