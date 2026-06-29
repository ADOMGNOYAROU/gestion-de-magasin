@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center py-5">
            <div class="empty-state-icon mb-4">
                <i class="fas fa-store"></i>
            </div>
            <h1 class="h4 fw-bold text-gray-800 mb-3">{{ __('messages.aucune_boutique_assignee') }}</h1>
            <p class="text-muted mb-4">{{ __('messages.aucune_boutique_description') }}</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('messages.retour_tableau_bord') }}
            </a>
        </div>
    </div>
</div>

<style>
.empty-state-icon {
    width: 80px; height: 80px; border-radius: 20px; margin: 0 auto;
    background: rgba(78, 115, 223, 0.12); color: #4e73df;
    display: flex; align-items: center; justify-content: center; font-size: 2rem;
}
</style>
@endsection
