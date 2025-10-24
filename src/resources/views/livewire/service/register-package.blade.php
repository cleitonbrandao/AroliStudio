<div>
    <form wire:submit="save" class="">
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
        
        <div class="grid grid-cols-12 gap-2">
            <div class="col-span-12 md:col-span-4 bg-gray-50">
                <div class="w-full p-2">
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

                <div class="w-full p-2">
                    <x-label for="price" value="{{ __('Preço') }}" />
                    <x-money-input 
                        id="price" 
                        wire-model="form.price"
                        placeholder="0,00"
                    />
                    @error('form.price')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <div class="w-full p-2">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('Detalhes') }}
                    </label>
                    <textarea 
                        id="description" 
                        wire:model="form.description"
                        rows="4" 
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                        placeholder="{{ __('Descrição do Pacote...') }}"
                    ></textarea>
                    @error('form.description')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <livewire:service.items-package />
            
            {{-- BOTÃO --}}
            <div class="col-span-12 justify-self-center">
                <x-button type="submit">
                    {{ __('Cadastrar') }}
                </x-button>
            </div>
        </div>
    </form>
</div>
