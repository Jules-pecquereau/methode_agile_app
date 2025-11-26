<x-app-layout>
    <x-slot name="header">
        <h2 class="mb-0 h4 font-weight-bold text-dark">
            {{ __('Calendrier des équipes') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container">

            <!-- Formulaire de planification -->
            <div class="mb-4 shadow-sm card">
                <div class="card-body">
                    <h3 class="mb-4 h5">Planifier une tâche</h3>

                    @if(session('success'))
                        <div class="mb-4 alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('calendar.schedule') }}"
                          x-data="{
                              selectedTaskIndex: '',
                              tasks: @json($schedulableTasks)
                          }"
                          class="row g-3 align-items-end">
                        @csrf

                        <!-- Tâche à planifier -->
                        <div class="col-md-4">
                            <label for="task_select" class="form-label">Tâche à planifier</label>
                            <select id="task_select" x-model="selectedTaskIndex" required class="form-select">
                                <option value="">Choisir une tâche</option>
                                @foreach($schedulableTasks as $index => $task)
                                    <option value="{{ $index }}">{{ $task['label'] }}</option>
                                @endforeach
                            </select>
                            @if(empty($schedulableTasks))
                                <div class="text-danger small mt-1">Aucune tâche assignée à une équipe n'a été trouvée.</div>
                            @endif

                            <!-- Champs cachés pour envoyer les IDs -->
                            <input type="hidden" name="team_id" :value="selectedTaskIndex !== '' ? tasks[selectedTaskIndex].team_id : ''">
                            <input type="hidden" name="task_id" :value="selectedTaskIndex !== '' ? tasks[selectedTaskIndex].task_id : ''">
                        </div>

                        <!-- Date -->
                        <div class="col-md">
                            <label for="start_date" class="form-label">Date</label>
                            <input type="date" name="start_date" id="start_date" required
                                   class="form-control">
                        </div>

                        <!-- Heure de début -->
                        <div class="col-md">
                            <label for="start_time" class="form-label">Heure de début</label>
                            <input type="time" name="start_time" id="start_time" required
                                   class="form-control">
                        </div>

                        <!-- Bouton -->
                        <div class="col-md">
                            <button type="submit" class="btn btn-primary w-100">
                                Planifier
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Calendrier -->
            <div class="shadow-sm card">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'fr',
                firstDay: 1, // Lundi
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                allDaySlot: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: {!! json_encode($events) !!}
            });
            calendar.render();
        });
    </script>
</x-app-layout>
