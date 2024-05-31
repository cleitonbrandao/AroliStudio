@extends('layouts.service.home')
@section('content')
    <form class="" method="POST" action="{{ route('register') }}">
        @csrf
{{--        PACOTE E ITEMS--}}
        <div class="grid grid-cols-12 gap-2">
{{--            PACOTE--}}
            <div class="col-span-12 md:col-span-4 bg-gray-50">
                    <div class="w-full p-2">
                        <x-label for="name" value="{{ __('Nome') }}" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required/>
                    </div>

                    <div class="w-full p-2">
                        <x-label for="price" value="{{ __('Preço') }}" />
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 light:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1M2 5h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm8 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                                </svg>
                            </div>
                            <input type="number" id="currency-input" class="block p-2.5 w-full z-20 ps-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-e-gray-700  light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:border-blue-500 shadow-sm" placeholder="R$" required>
                        </div>
                    </div>

                    <div class="w-full p-2">
                        <label for="describe" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Detalhes</label>
                        <textarea id="describe" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="Descrição do Pacote..."></textarea>
                    </div>
                </div>
{{--            ITEMS--}}
            <div class="col-span-12 md:col-span-8">
                <div class="flex-1 flex-col items-center bg-gray-50 rounded">
                    <div class="flex w-full shadow-lg z-10 bg-white">
                        <div class="start-1 w-12 h-12 p-2 cursor-pointer drop-shadow">
                            <svg class="fill-pink-300 hover:fill-pink-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 500">
                                <path d="M64 80c-8.8 0-16 7.2-16 16V416c0 8.8 7.2 16 16 16H384c8.8 0 16-7.2 16-16V96c0-8.8-7.2-16-16-16H64zM0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM200 344V280H136c-13.3 0-24-10.7-24-24s10.7-24 24-24h64V168c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H248v64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap gap-1 bg-gray-50 p-2">
                        <x-cards.services_products.card-service-product product_type="service" product_id="1" name="Foto 15 anos" price="1.000,00" type="Serviço"/>
                        <x-cards.services_products.card-service-product product_type="product" product_id="1" name="Album" price="500,00" type="Serviço"/>
                    </div>
                </div>
            </div>
            {{--        BOTÃO--}}
            <div class="col-span-12 justify-self-center">
                <x-button>
                    {{ __('Cadastrar') }}
                </x-button>
            </div>
        </div>
    </form>
@endsection
