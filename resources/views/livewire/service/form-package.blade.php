<form class="" method="POST" action="{{ route('root.register.package') }}">
    @csrf
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Erro:</span>
                {{ $error }}
            </div>
        @endforeach
    @endif
    <div class="grid grid-cols-12 gap-2">
        <div class="col-span-12 md:col-span-4 bg-gray-50">
                <div class="w-full p-2">
                    <x-label for="name" value="{{ __('Nome') }}" />
                    <x-input id="name" name="package[name]" class="block mt-1 w-full" type="text" :value="old('package.name')" required/>
                </div>

                <div class="w-full p-2">
                    <x-label for="price" value="{{ __('Preço') }}" />
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 light:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1M2 5h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm8 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                            </svg>
                        </div>
                        <input type="number" name="package[price]" value="{{ old('package.price') }}" id="price" class="block p-2.5 w-full z-20 ps-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-e-gray-700  light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:border-blue-500 shadow-sm" placeholder="R$">
                    </div>
                </div>

                <div class="w-full p-2">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Detalhes</label>
                    <textarea id="description" name="package[description]" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="Descrição do Pacote...">{{ old('description') }}</textarea>
                </div>
            </div>
        <livewire:service.items-package />
        {{--        BOTÃO--}}
        <div class="col-span-12 justify-self-center">
            <x-button>
                {{ __('Cadastrar') }}
            </x-button>
        </div>
    </div>
</form>
