<div>
    <form class="flex flex-col items-center justify-center w-full p-2" wire:submit="save">
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <span class="font-medium">Sucesso:</span>
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Erro:</span>
                    {{ $error }}
                </div>
            @endforeach
        @endif
        
{{--        NOME--}}
        <div class="flex flex-col w-full md:w-2/5 p-2">
            <x-label for="name" value="{{ __('Nome') }}" />
            <x-input id="name" class="block mt-1 w-full" type="text" wire:model="form.name" required/>
            @error('form.name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        
{{--        PREÇO E PREÇO DE CUSTO--}}
        <div class="flex flex-row flex-wrap items-end w-full md:w-2/5">
            <div class="w-full md:w-1/2 p-2">
                <x-label for="price" value="{{ __('Preço') }}" />
                <x-money-input 
                    id="price" 
                    wire-model="form.price"
                    placeholder="0,00"
                />
                @error('form.price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="w-full md:w-1/2 p-2">
                <x-label for="cost_price" value="{{ __('Preço de custo') }}" />
                <x-money-input 
                    id="cost_price" 
                    wire-model="form.cost_price"
                    placeholder="0,00"
                />
                @error('form.cost_price') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        
{{--        DETALHES--}}
        <div class="flex flex-col w-full md:w-2/5 p-2">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Detalhes</label>
            <textarea id="description" wire:model="form.description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Descrição do Produto..."></textarea>
            @error('form.description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        
{{--        BOTÃO--}}
        <div class="flex items-center justify-center w-full p-2">
            <x-button type="submit" class="ms-4">
                {{ __('Cadastrar') }}
            </x-button>
        </div>
    </form>
</div>