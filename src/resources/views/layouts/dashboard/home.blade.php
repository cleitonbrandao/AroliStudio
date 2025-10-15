<x-layouts.home 
    title="Dashboard"
    :navLinks="[
        ['text' => 'Home', 'route' => 'root.dashboard.hierarchy', 'active' => 'root.dashboard.home']
    ]"
>
    {{ $slot }}
</x-layouts.home>
