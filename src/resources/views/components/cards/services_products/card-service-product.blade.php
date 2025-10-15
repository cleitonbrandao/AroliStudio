@props(['name', 'price', 'type', 'product_id', 'product_type'])
<div class="flex-initial w-[200px] flex-col gap-1 p-1 bg-white border border-gray-200 rounded-lg shadow light:bg-gray-800 light:border-gray-700">
    <div class="flex">
        <a href="#">
            <input type="hidden" name="{{ $product_type }}[]" value="{{ $product_id }}"/>
            <h5 class="font-bold tracking-tight text-gray-900 light:text-white">{{ $name }}</h5>
        </a>
    </div>
    <div class="flex">
        <p class="font-normal text-gray-700 light:text-gray-400">{{ $price }}</p>
    </div>
    <div class="flex flex-row justify-between">
        <div>
            <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-400 rounded-lg hover:bg-red-500 focus:ring-4 focus:outline-none focus:ring-blue-300 light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800">
                Remover
                <svg class=" w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h11m0 0-4-4m4 4-4 4m-5 3H3a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h3"/>
                </svg>
            </a>
        </div>
        <div class="relative">
            <span class="absolute bottom-0 right-0 bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400">{{ $type }}</span>
        </div>
    </div>
</div>
