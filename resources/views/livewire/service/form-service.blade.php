@extends('layouts.service.home')
@section('content')
    <form class="flex flex-col items-center justify-center w-full p-2" method="POST" action="{{ route('root.register.service') }}">
        @csrf
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Erro:</span>
                    {{ $error }}
                </div>
            @endforeach
        @endif
{{--        NOME E TEMPO--}}
        <div class="flex flex-row flex-wrap items-end w-full  md:w-2/5">
            <div class="w-3/5 p-2">
                <x-label for="name" value="{{ __('Nome') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required/>
            </div>
            <div class="w-2/5 p-2">
                <label for="service_time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select time:</label>
                <div class="relative">
                    <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <input type="time" value="{{old('service_time')}}" name="service_time" id="service_time" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
            </div>
        </div>
{{--        PREÇO E PREÇO DE CUSTO--}}
        <div class="flex flex-row items-end w-full md:w-2/5">
            <div class="w-full p-2">
                <x-label for="price" value="{{ __('Preço') }}" />
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 light:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1M2 5h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm8 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                        </svg>
                    </div>
                    <x-input type="number" name="price" id="price" :value="old('price')" class="block p-2.5 w-full z-20 ps-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-e-gray-700  light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:border-blue-500 shadow-sm"/>
                </div>
            </div>

            <div class="w-full p-2">
                <x-label for="cost_price" value="{{ __('Preço de custo') }}" />
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 light:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1M2 5h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm8 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                        </svg>
                    </div>
                    <x-input type="number" name="cost_price" id="cost_price" :value="old('cost_price')" class="block p-2.5 w-full z-20 ps-10 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-e-gray-700  light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:border-blue-500 shadow-sm"/>
                </div>
            </div>
        </div>
{{--        DETALHES--}}
        <div class="w-full md:w-2/5 p-2">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Detalhes</label>
                <textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Descrição do Serviço...">{{old('description')}}</textarea>
            </div>
{{--        BOTÃO--}}
        <div class="flex items-center justify-center w-full p-2">
            <x-button class="ms-4">
                {{ __('Cadastrar') }}
            </x-button>
        </div>
    </form>
@endsection
