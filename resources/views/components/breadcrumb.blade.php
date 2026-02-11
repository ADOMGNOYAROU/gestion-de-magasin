@php
    $title = $title ?? 'Breadcrumb';
    $items = $items ?? [];
    $home = $home ?? ['url' => route('dashboard'), 'title' => 'Accueil'];
    $separator = $separator ?? '/';
@endphp

@if(!empty($items))
    <nav aria-label="{{ $title }}">
        <ol class="breadcrumb mb-0">
            <!-- Accueil -->
            <li class="breadcrumb-item">
                <a href="{{ $home['url'] }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i>{{ $home['title'] }}
                </a>
            </li>
            
            <!-- Éléments -->
            @foreach($items as $index => $item)
                @php
                    $isLast = $index === count($items) - 1;
                @endphp
                
                @if($isLast)
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $item['title'] }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        @if(isset($item['url']))
                            <a href="{{ $item['url'] }}" class="text-decoration-none">
                                {{ $item['title'] }}
                            </a>
                        @else
                            {{ $item['title'] }}
                        @endif
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
