@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Hero Section -->
    <div class="p-5 mb-4 bg-white border shadow-sm rounded-3">
        <div class="py-3 container-fluid">
            <h1 class="display-5 fw-bold text-primary">Gestion de Planning et Équipe</h1>
            <div class="gap-3 mt-4 d-flex">
                <a href="{{ route('tasks.index') }}" class="px-4 shadow-sm btn btn-primary btn-lg">
                    <i class="bi bi-list-check me-2"></i> Voir mes tâches
                </a>
                <a href="{{ route('calendar.index') }}" class="px-4 shadow-sm btn btn-outline-secondary btn-lg">
                    <i class="bi bi-calendar-week me-2"></i> Calendrier
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="row align-items-md-stretch">
        <div class="mb-4 col-md-6">
            <div class="p-4 transition-all bg-white border shadow-sm h-100 rounded-3 hover-shadow">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 50px; height: 50px;">
                    <i class="bi bi-kanban fs-4"></i>
                </div>
                <h2 class="h4">Gestion des Tâches</h2>
                <p class="text-muted">Créez, assignez et suivez l'avancement de vos tâches. Organisez votre travail par équipe et par priorité.</p>
                <a href="{{ route('tasks.index') }}" class="mt-2 btn btn-outline-primary">
                    Gérer les tâches <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="mb-4 col-md-6">
            <div class="p-4 transition-all bg-white border shadow-sm h-100 rounded-3 hover-shadow">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle" style="width: 50px; height: 50px;">
                    <i class="bi bi-calendar-check fs-4"></i>
                </div>
                <h2 class="h4">Planning Équipe</h2>
                <p class="text-muted">Visualisez la charge de travail de vos équipes. Évitez les conflits et optimisez les délais de livraison.</p>
                <a href="{{ route('calendar.index') }}" class="mt-2 btn btn-outline-success">
                    Voir le planning <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: all .3s ease;
    }
    .transition-all {
        transition: all .3s ease;
    }
</style>
@endsection
