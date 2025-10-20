<x-layouts.home 
    :title="__('app.employees')"
    :navLinks="[
        ['text' => __('app.list'), 'route' => 'root.employee.index', 'active' => 'root.employee.index'],
        ['text' => __('app.register'), 'route' => 'root.form.employee', 'active' => 'root.form.employee']
    ]"
>
    {{ $slot }}
</x-layouts.home>
