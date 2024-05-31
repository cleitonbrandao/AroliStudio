<x-app-layout >
    <x-slot name="header">
        <div class="flex flex-row">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Comercial') }}
            </h2>
            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <x-nav-link class="text-xs" href="{{ route('root.commercial.summary') }}" :active="request()->routeIs('root.commercial.summary')">
                    {{ __('Resumo de Caixa') }}
                </x-nav-link>
                <x-nav-link class="text-xs" href="{{ route('root.commercial.consumption') }}" :active="request()->routeIs('root.commercial.consumption')">
                    {{ __('Entradas de Consumo') }}
                </x-nav-link>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </div>
</x-app-layout>
