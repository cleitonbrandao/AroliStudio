<x-layouts.home 
    :title="__('app.dashboard')"
    :navLinks="[
        ['text' => __('app.home'), 'route' => 'root.dashboard.hierarchy', 'active' => 'root.dashboard.home']
    ]"
>
    {{ $slot }}
</x-layouts.home>
