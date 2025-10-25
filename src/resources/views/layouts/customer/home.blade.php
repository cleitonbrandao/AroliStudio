<x-layouts.home 
    :title="__('app.customers')"
    :navLinks="[
        ['text' => __('app.home'), 'route' => 'root.customers.index', 'active' => 'root.customers.index'],
        ['text' => __('app.list'), 'route' => 'root.customers.list', 'active' => 'root.customers.list'],
        ['text' => __('app.register'), 'route' => 'root.customers.create', 'active' => 'root.form.customers']
    ]"
>
    {{ $slot }}
</x-layouts.home>
