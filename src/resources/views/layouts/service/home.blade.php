<x-layouts.home 
    :title="__('app.services')"
    :navLinks="[
        ['text' => __('app.list'), 'route' => 'root.negotiable', 'active' => 'root.negotiable'],
        ['text' => __('app.register_products'), 'route' => 'root.form.product', 'active' => 'root.form.product'],
        ['text' => __('app.register_services'), 'route' => 'root.form.service', 'active' => 'root.form.service'],
        ['text' => __('app.register_packages'), 'route' => 'root.form.package', 'active' => 'root.form.package']
    ]"
>
    {{ $slot ?? '' }}
</x-layouts.home>