@php
    $headers = $headers ?? [];
    $rows = $rows ?? [];
    $actions = $actions ?? [];
    $emptyMessage = $emptyMessage ?? 'Aucune donnée disponible';
    $searchable = $searchable ?? true;
    $paginated = $paginated ?? true;
    $responsive = $responsive ?? true;
    $class = $class ?? '';
    $id = $id ?? 'table-' . uniqid();
@endphp

<div class="table-responsive {{ $responsive ? '' : '' }}">
    <table class="table table-striped table-hover datatable {{ $class }}" 
           id="{{ $id }}"
           @if($searchable) data-search="true" @endif
           @if($paginated) data-paginate="true" @endif>
        
        @if(!empty($headers))
        <thead class="table-light">
            <tr>
                @foreach($headers as $header)
                    <th class="{{ $header['class'] ?? '' }}">
                        {{ $header['title'] }}
                        @if(isset($header['sortable']) && $header['sortable'])
                            <i class="fas fa-sort ms-1 text-muted"></i>
                        @endif
                    </th>
                @endforeach
                
                @if(!empty($actions))
                    <th class="text-center">Actions</th>
                @endif
            </tr>
        </thead>
        @endif
        
        <tbody>
            @forelse($rows as $index => $row)
                <tr>
                    @foreach($headers as $key => $header)
                        <td class="{{ $header['class'] ?? '' }}">
                            @if(isset($header['callback']) && is_callable($header['callback']))
                                {{ $header['callback']($row, $index) }}
                            @elseif(isset($row[$key]))
                                {{ $row[$key] }}
                            @else
                                {{ $row->{$key} ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                    
                    @if(!empty($actions))
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                @foreach($actions as $action)
                                    @php
                                        $isVisible = true;
                                        if (isset($action['condition']) && is_callable($action['condition'])) {
                                            $isVisible = $action['condition']($row);
                                        }
                                    @endphp
                                    
                                    @if($isVisible)
                                        @if($action['type'] === 'link')
                                            <a href="{{ $action['url'] ?? '#' }}" 
                                               class="btn btn-sm {{ $action['class'] ?? 'btn-outline-primary' }}"
                                               @if(isset($action['title'])) title="{{ $action['title'] }}" @endif>
                                                <i class="{{ $action['icon'] ?? 'fas fa-eye' }}"></i>
                                                @if(isset($action['text']))
                                                    <span class="d-none d-md-inline ms-1">{{ $action['text'] }}</span>
                                                @endif
                                            </a>
                                        @elseif($action['type'] === 'button')
                                            <button type="{{ $action['buttonType'] ?? 'button' }}"
                                                    class="btn btn-sm {{ $action['class'] ?? 'btn-outline-primary' }}"
                                                    @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
                                                    @if(isset($action['title'])) title="{{ $action['title'] }}" @endif>
                                                <i class="{{ $action['icon'] ?? 'fas fa-edit' }}"></i>
                                                @if(isset($action['text']))
                                                    <span class="d-none d-md-inline ms-1">{{ $action['text'] }}</span>
                                                @endif
                                            </button>
                                        @elseif($action['type'] === 'form')
                                            <form method="{{ $action['method'] ?? 'POST' }}" 
                                                  action="{{ $action['url'] }}"
                                                  class="d-inline"
                                                  @if(isset($action['onsubmit'])) onsubmit="{{ $action['onsubmit'] }}" @endif>
                                                @csrf
                                                @if(isset($action['method']) && in_array(strtoupper($action['method']), ['PUT', 'PATCH', 'DELETE']))
                                                    @method($action['method'])
                                                @endif
                                                
                                                <button type="submit" 
                                                        class="btn btn-sm {{ $action['class'] ?? 'btn-outline-danger' }}"
                                                        @if(isset($action['title'])) title="{{ $action['title'] }}" @endif>
                                                    <i class="{{ $action['icon'] ?? 'fas fa-trash' }}"></i>
                                                    @if(isset($action['text']))
                                                        <span class="d-none d-md-inline ms-1">{{ $action['text'] }}</span>
                                                    @endif
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + (count($actions) > 0 ? 1 : 0) }}" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0">{{ $emptyMessage }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($searchable || $paginated)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableId = '{{ $id }}';
    const table = document.getElementById(tableId);
    
    if (table) {
        const dataTable = new DataTable(table, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json',
                search: "Rechercher:",
                lengthMenu: "Afficher _MENU_ éléments",
                info: "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                paginate: {
                    first: "Premier",
                    last: "Dernier",
                    next: "Suivant",
                    previous: "Précédent"
                },
                emptyTable: "{{ $emptyMessage }}",
                zeroRecords: "Aucun enregistrement trouvé"
            },
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']],
            @if(!$searchable)
            searching: false,
            @endif
            @if(!$paginated)
            paging: false,
            @endif
        });
        
        // Rendre la table globale pour un accès externe
        window.dataTables = window.dataTables || {};
        window.dataTables[tableId] = dataTable;
    }
});
</script>
@endif
