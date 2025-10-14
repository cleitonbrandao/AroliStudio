@props([
    'title' => '',
    'navLinks' => [], // [['text' => '', 'route' => '', 'active' => false]]
])

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $title }}
            </h2>
            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                @foreach ($navLinks as $link)
                    <x-nav-link class="text-xs" href="{{ route($link['route']) }}" :active="request()->routeIs($link['active'])">
                        {{ $link['text'] }}
                    </x-nav-link>
                @endforeach
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8 justify-center">
            {{ $slot }}
        </div>
    </div>
</x-app-layout>
