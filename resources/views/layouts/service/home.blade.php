<x-app-layout >
    <x-slot name="header">
        <div class="flex flex-row">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Serviços') }}
            </h2>
            <!-- Navigation Links -->
            <div class="space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <x-nav-link class="text-xs" href="{{ route('root.service.index') }}" :active="request()->routeIs('root.service.index')">
                    {{ __('Lista') }}
                </x-nav-link>
                <x-nav-link class="text-xs" href="{{ route('root.form.product') }}" :active="request()->routeIs('root.form.product')">
                    {{ __('Cadastrar - Produtos') }}
                </x-nav-link>
                <x-nav-link class="text-xs" href="{{ route('root.form.service') }}" :active="request()->routeIs('root.form.service')">
                    {{ __('Cadastrar - Serviços') }}
                </x-nav-link>
                <x-nav-link class="text-xs" href="{{ route('root.form.package') }}" :active="request()->routeIs('root.form.package')">
                    {{ __('Cadastrar - Pacotes') }}
                </x-nav-link>
            </div>
        </div>
    </x-slot>
    <div class="p-4 lg:px-8 justify-center">
        @yield('content')
    </div>
</x-app-layout>
