<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Méthode Agile') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-800 font-sans">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-10 top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-2xl font-bold text-indigo-600 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        AgileApp
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Tableau de bord</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Connexion</a>
                            {{-- @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">Inscription</a>
                            @endif --}}
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 bg-gradient-to-br from-indigo-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-extrabold text-gray-900 tracking-tight mb-6">
                Gérez vos projets <span class="text-indigo-600">Agiles</span> avec fluidité
            </h1>
            <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
                Une solution complète pour planifier, suivre et livrer vos projets en équipe.
                Optimisez votre workflow et concentrez-vous sur l'essentiel.
            </p>
            <div class="flex justify-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
                        Accéder à mon espace
                    </a>
                @else
                    {{-- <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
                        Commencer gratuitement
                    </a> --}}
                    <a href="{{ route('login') }}" class="bg-white text-indigo-600 border border-indigo-200 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-50 transition shadow-sm">
                        Se connecter
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900">Tout ce dont vous avez besoin</h2>
                <p class="mt-4 text-gray-600">Des outils puissants pour une gestion de projet sans friction.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="p-6 bg-gray-50 rounded-xl hover:shadow-md transition duration-300 border border-gray-100">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Gestion des Tâches</h3>
                    <p class="text-gray-600">Créez, assignez et suivez l'avancement de vos tâches en temps réel avec une interface intuitive.</p>
                </div>

                <!-- Feature 2 -->
                <div class="p-6 bg-gray-50 rounded-xl hover:shadow-md transition duration-300 border border-gray-100">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Collaboration d'Équipe</h3>
                    <p class="text-gray-600">Travaillez ensemble efficacement. Assignez des équipes aux tâches et évitez les conflits de planning.</p>
                </div>

                <!-- Feature 3 -->
                <div class="p-6 bg-gray-50 rounded-xl hover:shadow-md transition duration-300 border border-gray-100">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Calendrier Intelligent</h3>
                    <p class="text-gray-600">Visualisez votre planning global. Notre système gère automatiquement les week-ends et les disponibilités.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <span class="text-2xl font-bold">AgileApp</span>
                <p class="text-gray-400 text-sm mt-1">© {{ date('Y') }} Tous droits réservés.</p>
            </div>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white transition">À propos</a>
                <a href="#" class="text-gray-400 hover:text-white transition">Contact</a>
                <a href="#" class="text-gray-400 hover:text-white transition">Confidentialité</a>
            </div>
        </div>
    </footer>

</body>
</html>
