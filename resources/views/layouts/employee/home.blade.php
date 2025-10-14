<x-app-layout >
    <x-slot name="header">
        <div class="flex flex-row">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Funcionários') }}
            </h2>
            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <x-nav-link class="text-xs" href="{{ route('root.employee.index') }}" :active="request()->routeIs('root.employee.index')">
                    {{ __('Lista') }}
                </x-nav-link>
                <x-nav-link class="text-xs" href="{{ route('root.form.employee') }}" :active="request()->routeIs('root.form.employee')">
                    {{ __('Cadastrar') }}
                </x-nav-link>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8 justify-center">
            {{ $slot }}
        </div>
    </div>
</x-app-layout>
