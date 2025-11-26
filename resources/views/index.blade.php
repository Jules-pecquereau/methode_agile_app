@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="text-center col-md-8">
            <h1 class="mb-4 display-4">Bienvenue</h1>

            <div class="gap-3 d-grid d-sm-flex justify-content-sm-center">
                <a href="{{ route('tasks.index') }}" class="gap-3 px-4 btn btn-primary btn-lg">
                    <i class="bi bi-list-task"></i> Accéder aux Tâches
                </a>

            </div>
        </div>
    </div>
</div>
@endsection
