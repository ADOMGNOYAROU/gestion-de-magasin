@php
    $title = $title ?? 'Pagination';
    $data = $data ?? null;
    $showCount = $showCount ?? true;
    $showFirstLast = $showFirstLast ?? true;
    $align = $align ?? 'center'; // left, center, right
@endphp

@if($data && $data->hasPages())
    <nav aria-label="{{ $title }}">
        <div class="d-flex justify-content-{{ $align }} align-items-center">
            @if($showCount)
                <div class="me-3 text-muted">
                    <small>
                        Affichage de {{ $data->firstItem() ?? 0 }} à {{ $data->lastItem() ?? 0 }} 
                        sur {{ $data->total() }} résultats
                    </small>
                </div>
            @endif
            
            <ul class="pagination mb-0">
                <!-- Premier -->
                @if($showFirstLast)
                    <li class="page-item {{ $data->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $data->url(1) }}" tabindex="-1" aria-disabled="{{ $data->onFirstPage() ? 'true' : 'false' }}">
                            <i class="fas fa-angle-double-left"></i>
                            <span class="d-none d-md-inline ms-1">Premier</span>
                        </a>
                    </li>
                @endif
                
                <!-- Précédent -->
                <li class="page-item {{ $data->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $data->previousPageUrl() }}" tabindex="-1" aria-disabled="{{ $data->onFirstPage() ? 'true' : 'false' }}">
                        <i class="fas fa-angle-left"></i>
                        <span class="d-none d-md-inline ms-1">Précédent</span>
                    </a>
                </li>
                
                <!-- Pages -->
                @foreach($elements as $element)
                    @if(is_string($element))
                        <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                    @endif
                    
                    @if(isset($element['url']))
                        <li class="page-item {{ $element['active'] ? 'active' : '' }}">
                            <a class="page-link" href="{{ $element['url'] }}">{{ $element['label'] }}</a>
                        </li>
                    @endif
                @endforeach
                
                <!-- Suivant -->
                <li class="page-item {{ $data->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $data->nextPageUrl() }}" aria-disabled="{{ $data->hasMorePages() ? 'false' : 'true' }}">
                        <span class="d-none d-md-inline me-1">Suivant</span>
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>
                
                <!-- Dernier -->
                @if($showFirstLast)
                    <li class="page-item {{ $data->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $data->url($data->lastPage()) }}" aria-disabled="{{ $data->hasMorePages() ? 'false' : 'true' }}">
                            <span class="d-none d-md-inline me-1">Dernier</span>
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
