@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item active">Notifications</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Notifications</h1>
                @if($notifications->where('read_at', null)->count() > 0)
                    <form method="POST" action="{{ route('notifications.read_all') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-check-double me-1"></i> Tout marquer comme lu
                        </button>
                    </form>
                @endif
            </div>

            <div class="card">
                <div class="list-group list-group-flush" id="notificationsList">
                    @forelse($notifications as $notification)
                        <div class="list-group-item d-flex justify-content-between align-items-start {{ $notification->read_at ? '' : 'bg-light' }}">
                            <div>
                                <div class="fw-semibold">
                                    @if(!$notification->read_at)
                                        <span class="badge bg-danger me-2">Nouveau</span>
                                    @endif
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </div>
                                <div class="text-muted small">{{ $notification->data['message'] ?? '' }}</div>
                                <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link">
                                    {{ isset($notification->data['url']) ? 'Voir' : 'Marquer comme lu' }}
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-5">
                            Aucune notification pour le moment.
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
