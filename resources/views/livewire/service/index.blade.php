@extends('layouts.service.home')
@section('content')
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 light:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 light:bg-gray-700 light:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Serviços
                </th>
                <th scope="col" class="px-6 py-3">
                    Tipo
                </th>
                <th scope="col" class="px-6 py-3">
                    Categoria
                </th>
                <th scope="col" class="px-6 py-3">
                    Preço
                </th>
                <th scope="col" class="px-6 py-3">
                    Abrir
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($services as $service)
                <tr class="odd:bg-white odd:light:bg-gray-900 even:bg-gray-50 even:light:bg-gray-800 border-b light:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap light:text-white">
                        {{ $service->name }}
                    </th>
                    <td class="px-6 py-4">
                        Produto
                    </td>
                    <td class="px-6 py-4">
                        {{ $service->description }}
                    </td>
                    <td class="px-6 py-4">
                        R$ {{ $service->price }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-blue-600 light:text-blue-500 hover:underline">Abrir</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
