@props(['title', 'name' => 'modal-info'])

<x-modal :name="$name" maxWidth="lg">
    <div class="px-6 py-4">
        <div class="text-lg font-medium text-gray-900 mb-4">
            {{ $title }}
        </div>

        <div class="text-sm text-gray-600">
            {{ $body }}
        </div>
    </div>

    <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-end">
        <button 
            type="button" 
            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
            x-on:click="$dispatch('close-modal')"
        >
            Fechar
        </button>
    </div>
</x-modal>
