<x-layouts.home 
    :title="__('app.employees')"
    :navLinks="[
        ['text' => __('app.list'), 'route' => 'root.employee.index', 'active' => 'root.employee.index'],
        ['text' => __('app.register'), 'route' => 'root.employee.create', 'active' => 'root.form.employee']
    ]"
>
    {{ $slot }}
</x-layouts.home>
