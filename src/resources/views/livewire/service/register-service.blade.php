<div>
    <form wire:submit="save" class="flex flex-col items-center justify-center w-full p-2">
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <span class="font-medium">Sucesso:</span>
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Erro:</span>
                {{ session('error') }}
            </div>
        @endif
        
        <div class="flex flex-row flex-wrap items-end w-full md:w-2/5">
            <div class="w-3/5 p-2">
                <x-label for="name" value="{{ __('Nome') }}" />
                <x-input 
                    id="name" 
                    class="block mt-1 w-full" 
                    type="text" 
                    wire:model="form.name"
                    required
                />
                @error('form.name')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="w-2/5 p-2">
                <label for="service_time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('Tempo de serviço') }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <input 
                        type="time" 
                        wire:model="form.service_time"
                        id="service_time" 
                        class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                    />
                </div>
                @error('form.service_time')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="flex flex-row items-end w-full md:w-2/5">
            <div class="w-full p-2">
                <x-label for="price" value="{{ __('Preço') }}" />
                <x-money-input 
                    id="price" 
                    wire-model="form.price"
                    placeholder="0,00"
                    required
                />
                @error('form.price')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="w-full p-2">
                <x-label for="cost_price" value="{{ __('Preço de custo') }}" />
                <x-money-input 
                    id="cost_price" 
                    wire-model="form.cost_price"
                    placeholder="0,00"
                    required
                />
                @error('form.cost_price')
                    <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="w-full md:w-2/5 p-2">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                {{ __('Detalhes') }}
            </label>
            <textarea 
                id="description" 
                wire:model="form.description"
                rows="4" 
                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                placeholder="{{ __('Descrição do Serviço...') }}"
            ></textarea>
            @error('form.description')
                <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="flex items-center justify-center w-full p-2">
            <x-button class="ms-4" type="submit">
                {{ __('Cadastrar') }}
            </x-button>
        </div>
    </form>
</div>
