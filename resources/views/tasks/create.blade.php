{{-- filepath: c:\code\methode_agile_app\resources\views\tasks\create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-plus-circle"></i> Créer une Tâche
                        </h4>
                        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-light">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">
                                Nom de la tâche <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
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
                                      placeholder="Décrivez les détails de la tâche...">{{ old('description') }}</textarea>
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
                                   value="{{ old('expected_minutes') }}"
                                   min="1"
                                   placeholder="Ex: 120"
                                   required>
                            @error('expected_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Temps estimé pour compléter cette tâche</small>
                        </div>

                        <div class="mb-4">
                            <label for="start_at" class="form-label fw-bold">
                                Date et heure de début
                            </label>
                            <input type="datetime-local"
                                   class="form-control @error('start_at') is-invalid @enderror"
                                   id="start_at"
                                   name="start_at"
                                   value="{{ old('start_at') }}">
                            @error('start_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Si renseigné, la tâche apparaîtra automatiquement dans le calendrier.</small>
                        </div>

                        <script>
                            document.getElementById('start_at').addEventListener('change', function(e) {
                                const date = new Date(this.value);
                                const day = date.getDay();
                                if (day === 0 || day === 6) {
                                    alert('Les tâches ne peuvent pas être planifiées le week-end (Samedi ou Dimanche).');
                                    this.value = '';
                                }
                            });
                        </script>

                        <div class="mb-4">
                            <label for="user_id" class="form-label fw-bold">
                                Assigner au salarié <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('user_id') is-invalid @enderror"
                                    id="user_id"
                                    name="user_id"
                                    required>
                                <option value="" selected disabled>Choisir un salarié...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="alert alert-info mt-2 mb-0" role="alert">
                                <i class="bi bi-info-circle"></i>
                                <strong>Note :</strong> La tâche sera assignée aux équipes dont fait partie ce salarié.
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               role="switch"
                                               id="active"
                                               name="active"
                                               value="1"
                                               {{ old('active') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="active">
                                            <i class="bi bi-toggle-on text-success"></i> Activer la tâche
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle"></i>
                                        La tâche ne sera active que si le salarié appartient à au moins une équipe.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Créer la Tâche
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
