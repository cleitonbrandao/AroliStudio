<div class="p-4 bg-gray-50 rounded shadow-sm">
    <div class="w-full text-center p-3 rounded-t-lg bg-gray-200 text-slate-950 font-bold text-lg"><h1>Pacotes</h1></div>
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Pacotes
                </th>
                <th scope="col" class="px-6 py-3">
                    Descrição
                </th>
                <th scope="col" class="px-6 py-3">
                    Preço
                </th>
                <th scope="col" class="px-6 py-3">
                    Editar
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($packages as $package)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $package->name }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $package->description }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $package->price }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-blue-600 light:text-blue-500 hover:underline">Editar</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="p-2">
            {{ $packages->links('pagination') }}
        </div>
    </div>
</div>

