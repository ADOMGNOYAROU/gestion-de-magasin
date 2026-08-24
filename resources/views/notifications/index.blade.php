@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Notifications</h1>
                @if ($notifications->where('read_at', null)->count() > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-check-double me-1"></i> Tout marquer comme lu
                        </button>
                    </form>
                @endif
            </div>

            <div class="card">
                <div class="list-group list-group-flush">
                    @forelse ($notifications as $notification)
                        <div class="list-group-item d-flex justify-content-between align-items-start {{ $notification->read_at ? '' : 'bg-light' }}">
                            <div>
                                <div class="fw-{{ $notification->read_at ? 'normal' : 'bold' }}">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </div>
                                <div class="text-muted small">{{ $notification->data['message'] ?? '' }}</div>
                                <div class="text-muted small mt-1">{{ $notification->created_at->format('d/m/Y à H:i') }}</div>
                            </div>
                            @unless ($notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none">Marquer comme lu</button>
                                </form>
                            @endunless
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            Aucune notification.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-3">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
