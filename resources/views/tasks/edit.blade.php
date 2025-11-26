{{-- filepath: c:\code\methode_agile_app\resources\views\tasks\edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square"></i> Modifier la Tâche
                        </h4>
                        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-dark">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">
                                Nom de la tâche <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $task->name) }}"
                                   placeholder="Ex: Développer la fonctionnalité X"
                                   required
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Décrivez les détails de la tâche...">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Facultatif - Ajoutez des détails supplémentaires</small>
                        </div>

                        <div class="mb-4">
                            <label for="expected_minutes" class="form-label fw-bold">
                                Durée estimée (en minutes) <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   class="form-control @error('expected_minutes') is-invalid @enderror"
                                   id="expected_minutes"
                                   name="expected_minutes"
                                   value="{{ old('expected_minutes', $task->expected_minutes) }}"
                                   min="1"
                                   placeholder="Ex: 120"
                                   required>
                            @error('expected_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Temps estimé pour compléter cette tâche</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Équipes associées <span class="text-danger">*</span>
                            </label>
                            <div class="border rounded p-3 bg-light">
                                @php
                                    $selectedTeams = old('teams', $task->teams->pluck('id')->toArray());
                                @endphp
                                @forelse($teams as $team)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="teams[]"
                                               value="{{ $team->id }}"
                                               id="team_{{ $team->id }}"
                                               {{ in_array($team->id, $selectedTeams) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="team_{{ $team->id }}">
                                            <i class="bi bi-people-fill text-info"></i> {{ $team->name }}
                                        </label>
                                    </div>
                                @empty
                                    <div class="alert alert-warning mb-0" role="alert">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Aucune équipe disponible. Veuillez créer des équipes d'abord.
                                    </div>
                                @endforelse
                            </div>
                            <div class="alert alert-info mt-2 mb-0" role="alert">
                                <i class="bi bi-info-circle"></i>
                                <strong>Important :</strong> Si aucune équipe n'est cochée, la tâche sera automatiquement inactive.
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="card border-{{ $task->active ? 'success' : 'secondary' }}">
                                <div class="card-body">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               role="switch"
                                               id="active"
                                               name="active"
                                               value="1"
                                               {{ old('active', $task->active) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="active">
                                            @if(old('active', $task->active))
                                                <i class="bi bi-toggle-on text-success"></i> Tâche active
                                            @else
                                                <i class="bi bi-toggle-off text-secondary"></i> Tâche inactive
                                            @endif
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle"></i>
                                        La tâche ne sera active que si au moins une équipe est sélectionnée.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-light border" role="alert">
                            <strong><i class="bi bi-clock-history"></i> Informations :</strong>
                            <ul class="mb-0 mt-2">
                                <li>Créée le : <strong>{{ $task->created_at?->translatedFormat('d F Y à H:i') }}</strong></li>
                                <li>Dernière modification : <strong>{{ $task->updated_at?->translatedFormat('d F Y à H:i') }}</strong></li>
                                <li>Il y a : <strong>{{ $task->updated_at?->diffForHumans() }}</strong></li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
