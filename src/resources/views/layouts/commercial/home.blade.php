<x-layouts.home 
    :title="__('app.commercial')"
    :navLinks="[
        ['text' => __('app.cash_summary'), 'route' => 'root.commercial.summary', 'active' => 'root.commercial.summary'],
        ['text' => __('app.consumption_entries'), 'route' => 'root.commercial.consumption', 'active' => 'root.commercial.consumption'],
        ['text' => __('app.companies_suppliers'), 'route' => '#', 'active' => 'root.commercial.consumption']
    ]"
>
    {{ $slot ?? '' }}
</x-layouts.home>
