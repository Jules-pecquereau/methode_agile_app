<section>
    <header class="mb-4">
        <h4 class="h5">Informations du profil</h4>
        <p class="text-muted small">
            Mettez à jour les informations de votre profil et votre adresse email.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" {{ auth()->user()->role !== 'manager' ? 'disabled' : '' }}>
            @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username" {{ auth()->user()->role !== 'manager' ? 'disabled' : '' }}>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-muted small">
                        Votre adresse email n'est pas vérifiée.

                        <button form="send-verification" class="btn btn-link p-0 align-baseline text-decoration-none">
                            Cliquez ici pour renvoyer l'email de vérification.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-success small">
                            Un nouveau lien de vérification a été envoyé à votre adresse email.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        @if(auth()->user()->role === 'manager')
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Enregistrer</button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small fade-message">Enregistré.</span>
                <script>
                    setTimeout(function() {
                        document.querySelector('.fade-message').style.display = 'none';
                    }, 2000);
                </script>
            @endif
        </div>
        @else
        <div class="alert alert-info small">
            <i class="bi bi-info-circle me-2"></i>Seuls les managers peuvent modifier les informations de profil.
        </div>
        @endif
    </form>
</section>
