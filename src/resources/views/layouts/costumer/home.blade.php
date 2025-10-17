<x-layouts.home 
    :title="__('app.customers')"
    :navLinks="[
        ['text' => __('app.list'), 'route' => 'root.costumer.index', 'active' => 'root.costumer.index'],
        ['text' => __('app.register'), 'route' => 'root.form.costumer', 'active' => 'root.form.costumer']
    ]"
>
    {{ $slot ?? '' }}
</x-layouts.home>
