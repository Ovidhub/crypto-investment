@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between border-t border-light-200 dark:border-dark-200/50 pt-4">
        <!-- Mobile Pagination -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-dark-400 dark:text-light-500 bg-light-100 dark:bg-dark-200/50 border border-light-200 dark:border-dark-200/50 cursor-default leading-5 rounded-lg">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-dark dark:text-white bg-white dark:bg-dark-200/50 border border-light-200 dark:border-dark-200/50 leading-5 rounded-lg hover:bg-light-50 dark:hover:bg-dark-200 transition-all duration-150">
                    Previous
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-dark dark:text-white bg-white dark:bg-dark-200/50 border border-light-200 dark:border-dark-200/50 leading-5 rounded-lg hover:bg-light-50 dark:hover:bg-dark-200 transition-all duration-150">
                    Next
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-dark-400 dark:text-light-500 bg-light-100 dark:bg-dark-200/50 border border-light-200 dark:border-dark-200/50 cursor-default leading-5 rounded-lg">
                    Next
                </span>
            @endif
        </div>

        <!-- Desktop Pagination -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-dark-400 dark:text-light-500">
                    Showing
                    <span class="font-medium text-dark dark:text-white">{{ $paginator->firstItem() }}</span>
                    to
                    <span class="font-medium text-dark dark:text-white">{{ $paginator->lastItem() }}</span>
                    of
                    <span class="font-medium text-dark dark:text-white">{{ $paginator->total() }}</span>
                    transactions
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-lg shadow-sm space-x-2">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Previous">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-dark-400 dark:text-light-500 bg-light-100 dark:bg-dark-200/50 border border-light-200 dark:border-dark-200/50 cursor-default rounded-lg" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-dark dark:text-white bg-white dark:bg-dark-200/50 border border-light-200 dark:border-dark-200/50 rounded-lg hover:bg-primary hover:text-white hover:border-primary dark:hover:bg-primary transition-all duration-150" aria-label="Previous">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-dark-400 dark:text-light-500 bg-light-100 dark:bg-dark-200/50 border border-light-200 dark:border-dark-200/50 cursor-default rounded-lg">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-primary border border-primary cursor-default rounded-lg shadow-lg">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-dark-400 dark:text-light-400 bg-white dark:bg-dark-200/50 border border-light-200 dark:border-dark-200/50 rounded-lg hover:bg-light-50 dark:hover:bg-dark-200 hover:text-dark dark:hover:text-white hover:border-primary transition-all duration-150" aria-label="Go to page {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-dark dark:text-white bg-white dark:bg-dark-200/50 border border-light-200 dark:border-dark-200/50 rounded-lg hover:bg-primary hover:text-white hover:border-primary dark:hover:bg-primary transition-all duration-150" aria-label="Next">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Next">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-dark-400 dark:text-light-500 bg-light-100 dark:bg-dark-200/50 border border-light-200 dark:border-dark-200/50 cursor-default rounded-lg" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
