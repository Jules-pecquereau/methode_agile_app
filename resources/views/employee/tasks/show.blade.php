<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold mb-0">
                {{ __('Détails de la tâche') }}
            </h2>
            <a href="{{ route('employee.tasks.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title h2 mb-4">{{ $task->name }}</h3>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted mb-3">Informations générales</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Durée estimée</span>
                                    <span class="fw-bold">{{ $task->expected_minutes }} min</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Début planifié</span>
                                    <span class="fw-bold">{{ $task->start_at ? $task->start_at->format('d/m/Y H:i') : 'Non planifié' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Statut</span>
                                    @if($task->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted mb-3">Équipes assignées</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($task->teams as $team)
                                    <span class="badge bg-info text-dark fs-6">{{ $team->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if($task->description)
                        <div class="mb-4">
                            <h5 class="text-muted mb-3">Description</h5>
                            <div class="p-3 bg-light rounded border">
                                {{ $task->description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
