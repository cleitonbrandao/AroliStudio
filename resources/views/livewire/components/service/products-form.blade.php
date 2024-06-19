{{--<div class="flex flex-col w-full h-full p-2">--}}
    <form class="flex flex-col items-center justify-center w-full p-2" method="POST" action="{{ $this->product ? route('root.update.product') : route('root.register.product') }}">
        @csrf
        @method('PUT')
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Erro:</span>
                    {{ $error }}
                </div>
            @endforeach
        @endif
        @isset($this->product)
            <input type="hidden" name="id" value="{{ $this->product->id }}">
        @endisset
        {{--        NOME--}}
        <div class="flex flex-col w-full p-2">
            <x-label for="name" value="{{ __('Nome') }}" />
            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name') ?? $this->product->name" required/>
        </div>
        {{--        PREÇO E PREÇO DE CUSTO--}}
        <div class="flex flex-row flex-wrap items-end w-full">
            <div class="w-full md:w-1/2 p-2">
                <x-label for="price" value="{{ __('Preço') }}" />
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 light:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1M2 5h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm8 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                        </svg>
                    </div>
                    <input type="text" name="price" id="price" value="{{ old('price') ?? $this->product->price }}" class="block p-2.5 w-full z-20 ps-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-e-gray-700  light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:border-blue-500 shadow-sm" placeholder="R$" />
                </div>
            </div>

            <div class="w-full md:w-1/2 p-2">
                <x-label for="cost_price" value="{{ __('Preço de custo') }}" />
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 light:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1M2 5h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm8 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                        </svg>
                    </div>
                    <input type="text" name="cost_price" id="cost_price" value="{{ old('cost_price') ?? $this->product->cost_price }}" class="block p-2.5 w-full z-20 ps-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-e-gray-700  light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:border-blue-500 shadow-sm" placeholder="R$">
                </div>
            </div>
        </div>
        {{--        DETALHES--}}
        <div class="flex flex-col w-full p-2">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Detalhes</label>
            <textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Descrição do Produto...">{{ old('description') ?? $this->product->description }}</textarea>
        </div>
        {{--        BOTÃO--}}
        <div class="flex items-center justify-center w-full p-2">
            <x-button class="ms-4">
                {{ __('Cadastrar') }}
            </x-button>
        </div>
    </form>
{{--</div>--}}

