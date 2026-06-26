@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between gap-4 px-1">

        <p class="text-xs text-gray-500">
            Mostrando <span class="font-semibold text-gray-700">{{ $paginator->firstItem() ?? 0 }}</span>
            a <span class="font-semibold text-gray-700">{{ $paginator->lastItem() ?? 0 }}</span>
            de <span class="font-semibold text-gray-700">{{ $paginator->total() }}</span> registros
        </p>

        <span class="inline-flex items-center gap-1">

            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            {{-- Páginas --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-bold text-white"
                                  style="background-color: #0F4229;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-colors duration-150">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </span>
    </nav>
@endif
