@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
        <div>
            <p style="color: #666; font-size: 0.9em;">
                Mostrando
                <strong>{{ $paginator->firstItem() }}</strong>
                a
                <strong>{{ $paginator->lastItem() }}</strong>
                de
                <strong>{{ $paginator->total() }}</strong>
                resultados
            </p>
        </div>

        <div style="display: flex; gap: 10px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span style="padding: 8px 16px; background-color: #f5f5f5; color: #999; border-radius: 4px; cursor: not-allowed;">
                    ← Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="padding: 8px 16px; background-color: #333; color: white; border-radius: 4px; text-decoration: none; transition: background-color 0.2s;">
                    ← Anterior
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="padding: 8px 16px; background-color: #333; color: white; border-radius: 4px; text-decoration: none; transition: background-color 0.2s;">
                    Próximo →
                </a>
            @else
                <span style="padding: 8px 16px; background-color: #f5f5f5; color: #999; border-radius: 4px; cursor: not-allowed;">
                    Próximo →
                </span>
            @endif
        </div>
    </nav>
@endif
