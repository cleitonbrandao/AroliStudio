@props([
    'title' => '',
    'blur' => false,
    'wireModel' => null
])
<div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">{{ $title }}</h2>
            <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="$dispatch('close')">&times;</button>
        </div>
        <div>
            {{ $slot }}
        </div>
        @if (isset($footer))
            <div class="mt-6 border-t pt-4">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
