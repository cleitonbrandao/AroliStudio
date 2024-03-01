<x-app-layout >
    <x-slot name="header">
        <div class="flex flex-row">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Clinetes') }}
            </h2>
            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <x-nav-link class="text-xs" href="{{ route('root.costumer.index') }}" :active="request()->routeIs('root.costumer.index')">
                    {{ __('Lista') }}
                </x-nav-link>
                <x-nav-link class="text-xs" href="{{ route('root.form.costumer') }}" :active="request()->routeIs('root.form.costumer')">
                    {{ __('Cadastrar') }}
                </x-nav-link>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8 justify-center">
            @yield('content')
        </div>
    </div>
</x-app-layout>
