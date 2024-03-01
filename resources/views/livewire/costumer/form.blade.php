@extends('layouts.costumer.home')
@section('content')
    <form class="flex flex-wrap w-full md:w-1/2 p-2 justify-evenly" method="POST" action="{{ route('root.register.costumer') }}">
        @csrf
        @if ($errors->any())
                @foreach ($errors->all() as $error)
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Erro!</span>git
                    {{ $error }}
                </div>
            @endforeach
        @endif
        {{--        NOME E SOBRE NOME--}}
        <div class="flex flex-col sm:flex-row flex-wrap justify-center w-full">
            <div class="w-full sm:w-1/2 p-2">
                <x-label for="name" value="{{ __('Nome') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="person[name]" :value="old('person.name')" required/>
            </div>

            <div class="w-full sm:w-1/2 p-2">
                <x-label for="last_name" value="{{ __('Sobre Nome') }}" />
                <x-input id="last_name" class="block mt-1 w-full" type="text" name="person[last_name]" :value="old('person.last_name')" required/>
            </div>
        </div>
        {{--        CPF E DATA DE NASCIMENTO--}}
        <div class="flex flex-col sm:flex-row flex-wrap items-center justify-evenly w-full">
            <div class="w-full sm:w-1/2 p-2">
                <x-label for="cpf" value="{{ __('CPF') }}" />
                <x-input id="cpf" class="block mt-1 w-full" type="text" name="costumer[cpf]" autocomplete="cpf" :value="old('costumer.cpf')" />
            </div>
            <div class="w-full sm:w-1/2 p-2">
                <x-label>Data de Nasciemnto</x-label>
                <x-datepicker name="costumer[birthday]" :old="old('costumer.birthday')"/>
            </div>
        </div>
        {{--        CONTATO E-MAIL--}}
        <div class="flex flex-col sm:flex-row flex-wrap justify-center w-full">
            <div class="w-full sm:w-1/2 p-2">
                <label for="phone-input" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Contato:</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 light:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 19 18">
                            <path d="M18 13.446a3.02 3.02 0 0 0-.946-1.985l-1.4-1.4a3.054 3.054 0 0 0-4.218 0l-.7.7a.983.983 0 0 1-1.39 0l-2.1-2.1a.983.983 0 0 1 0-1.389l.7-.7a2.98 2.98 0 0 0 0-4.217l-1.4-1.4a2.824 2.824 0 0 0-4.218 0c-3.619 3.619-3 8.229 1.752 12.979C6.785 16.639 9.45 18 11.912 18a7.175 7.175 0 0 0 5.139-2.325A2.9 2.9 0 0 0 18 13.446Z"/>
                        </svg>
                    </div>
                    <x-input type="tel" id="phone-input" name="person[phone]" :value="old('people.phone')" aria-describedby="helper-text-explanation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="999-999-9999" />
                </div>
            </div>

            <div class="w-full sm:w-1/2 p-2">
                <label for="input-group-1" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">E-mail</label>
                <div class="relative mb-6">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 light:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                            <path d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.987 1.987 0 0 0 .641.541l9.395 7.737Z"/>
                            <path d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z"/>
                        </svg>
                    </div>
                    <x-input type="text" id="input-group-1" name="costumer[email]" :value="old('costumer.email')" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="exemplo@email.com" />
                </div>
            </div>
        </div>
        {{--        FOTO--}}
        <div class="w-full p-2">
            <label class="block mb-2 text-sm font-medium text-gray-900 light:text-white" for="file_input">Foto "Opicional"</label>
            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 light:text-gray-400 focus:outline-none light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400" aria-describedby="file_input_help" id="file_input" type="file" name="person[photo]">
            <p class="mt-1 text-sm text-gray-500 light:text-gray-300" id="file_input_help">SVG, PNG, JPG or GIF (MAX. 800x400px).</p>

        </div>

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="w-2/5 p-2">
                <x-label for="terms">
                    <div class="flex items-center">
                        <x-checkbox name="terms" id="terms" required />

                        <div class="ms-2">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                            ]) !!}
                        </div>
                    </div>
                </x-label>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row items-center justify-center w-full p-2">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer" x-data x-on:click="$dispatch('open-modal', {name:'terms'})">
                {{ __('Termos de Cadastro') }}
            </a>

            <x-button class="ms-4">
                {{ __('Cadastro') }}
            </x-button>
        </div>
    </form>
    <x-modal-info title="{{__('Termos do Cadastro')}}" name="terms">
    <x-slot name="body">
        With less than a month to go before the European Union enacts new consumer privacy laws for its citizens, companies around the world are updating their terms of service agreements to comply.
        The European Union’s General Data Protection Regulation (G.D.P.R.) goes into effect on May 25 and is meant to ensure a common set of data rights in the European Union. It requires organizations to notify users as soon as possible of high-risk data breaches that could personally affect them.
    </x-slot>
</x-modal-info>
@endsection
