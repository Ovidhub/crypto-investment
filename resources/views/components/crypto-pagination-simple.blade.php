@if ($paginator->hasPages())
    <nav class="flex items-center justify-center space-x-2">
        {{-- Previous Button --}}
        @if ($paginator->onFirstPage())
            <button disabled class="px-4 py-2 text-sm font-medium text-gray-500 bg-gray-800/50 border border-gray-700 rounded-lg cursor-not-allowed">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 border border-gray-700 rounded-lg hover:bg-primary-500 hover:border-primary-500 transition-all duration-150">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>
        @endif

        {{-- Page Numbers --}}
        <div class="flex items-center space-x-1">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-2 text-sm text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-4 py-2 text-sm font-bold text-white bg-primary-500 border border-primary-500 rounded-lg shadow-lg">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:border-primary-500 hover:text-white transition-all duration-150">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Button --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 border border-gray-700 rounded-lg hover:bg-primary-500 hover:border-primary-500 transition-all duration-150">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        @else
            <button disabled class="px-4 py-2 text-sm font-medium text-gray-500 bg-gray-800/50 border border-gray-700 rounded-lg cursor-not-allowed">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        @endif
    </nav>
    
    {{-- Page Info --}}
    <div class="text-center mt-3">
        <p class="text-sm text-gray-400">
            Page <span class="font-semibold text-white">{{ $paginator->currentPage() }}</span> of <span class="font-semibold text-white">{{ $paginator->lastPage() }}</span>
            <span class="mx-2">•</span>
            <span class="font-medium text-white">{{ $paginator->total() }}</span> total cryptocurrencies
        </p>
    </div>
@endif
