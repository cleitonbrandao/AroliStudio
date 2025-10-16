@props([
    'title' => '',
    'navLinks' => [], // [['text' => '', 'route' => '', 'active' => false]]
])

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center">
            <div class="flex flex-row items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-200">
                    {{ $title }}
                </h2>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @foreach ($navLinks as $link)
                        @php
                            $isRoute = isset($link['route']) && $link['route'] !== '#' && !str_starts_with($link['route'], 'http');
                            $href = $isRoute ? route($link['route']) : ($link['route'] ?? '#');
                        @endphp
                        <x-nav-link class="text-xs" href="{{ $href }}" :active="$isRoute ? request()->routeIs($link['active']) : false">
                            {{ $link['text'] }}
                        </x-nav-link>
                    @endforeach
                </div>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8 justify-center">
            {{ $slot }}
        </div>
    </div>
</x-app-layout>
