    <div class="py-2">
        @if($paginator->hasPages())
        <nav aria-label="Page navigation example">
            <ul class="inline-flex -space-x-px text-sm">
                @if($paginator->onFirstPage())
                    <li>
                        <a class="cursor-not-allowed flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-200 bg-gray-100 border border-e-0 border-gray-300 rounded-s-lg dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">Previous</a>
                    </li>
                @else
                    <li>
                        <a  wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="cursor-pointer flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Previous</a>
                    </li>
                @endif
                @foreach($elements as $page)
                    @foreach($page as $number => $url)
                            <li>
                                <a href="{{ $url }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">{{ $number }}</a>
                            </li>
                    @endforeach
                @endforeach
                @if($paginator->onLastPage())
                    <li>
                        <a class="cursor-not-allowed flex items-center justify-center px-3 h-8 leading-tight text-gray-200 bg-gray-100 border border-gray-300 rounded-e-lg dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">Next</a>
                    </li>
                @else
                    <li>
                        <a wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="cursor-pointer flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Next</a>
                    </li>
                @endif
            </ul>
        </nav>
        @endif
    </div>
