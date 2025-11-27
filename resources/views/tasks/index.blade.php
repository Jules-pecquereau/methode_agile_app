{{-- filepath: c:\code\methode_agile_app\resources\views\tasks\index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h1>Gestion des Tâches</h1>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nouvelle Tâche
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="mb-3 btn-group">
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">Toutes</a>
        <a href="{{ route('tasks.index', ['status' => 'active']) }}" class="btn btn-outline-success {{ request('status') === 'active' ? 'active' : '' }}">Actives</a>
        <a href="{{ route('tasks.index', ['status' => 'inactive']) }}" class="btn btn-outline-danger {{ request('status') === 'inactive' ? 'active' : '' }}">Inactives</a>
        <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="btn btn-outline-info {{ request('status') === 'completed' ? 'active' : '' }}">Terminées</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Début</th>
                        <th>Durée estimée</th>
                        <th>Salarié</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>
                                <strong>{{ $task->name }}</strong>
                                @if($task->description)
                                    <br><small class="text-muted">{{ Str::limit($task->description, 60) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($task->start_at)
                                    {{ $task->start_at->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $task->expected_minutes }} min</td>
                            <td>
                                @foreach($task->users as $user)
                                    <span class="badge bg-info">{{ $user->name }}</span>
                                @endforeach
                                @if($task->users->isEmpty())
                                    <span class="text-muted">Aucun</span>
                                @endif
                            </td>
                            <td>
                                @if($task->completed_at)
                                    <span class="badge bg-info">
                                        <i class="bi bi-check-circle"></i> Terminée
                                    </span>
                                    <br><small class="text-muted">{{ $task->completed_at->format('d/m/Y H:i') }}</small>
                                @elseif($task->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning">
                                    Modifier
                                </a>
                                @if($task->active)
                                    <form action="{{ route('tasks.deactivate', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Désactiver cette tâche ?')">
                                            Désactiver
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Aucune tâche trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $tasks->links() }}
    </div>
</div>
@endsection
