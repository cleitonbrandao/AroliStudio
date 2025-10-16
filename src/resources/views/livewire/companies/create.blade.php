<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="border-4 border-dashed border-gray-200 rounded-lg p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Criar Nova Empresa</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Crie uma nova empresa para começar a gerenciar seus negócios.
                </p>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="createCompany" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nome da Empresa
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               wire:model="name" 
                               id="name" 
                               name="name"
                               class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                               placeholder="Digite o nome da empresa">
                    </div>
                    @error('name') 
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('companies.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Criar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
