@props(['name' => 'modal-card'])

<x-modal :name="$name" maxWidth="lg">
    <div class="px-6 py-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">
                {{ $title ?? 'Modal' }}
            </h3>
            <button 
                type="button" 
                class="text-gray-400 hover:text-gray-600"
                x-on:click="$dispatch('close-modal')"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="text-sm text-gray-600">
            {{ $slot }}
        </div>
    </div>

    @if(isset($footer))
        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-end">
            {{ $footer }}
        </div>
    @endif
</x-modal>
