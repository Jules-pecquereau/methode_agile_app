<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold">
            {{ __('Mes Tâches') }}
        </h2>
    </x-slot>

    <div class="py-4" x-data="{ view: 'list' }">
        <div class="container">

            <!-- View Toggle -->
            <div class="mb-4 d-flex justify-content-end">
                <div class="btn-group" role="group">
                    <button type="button"
                            @click="view = 'list'"
                            :class="{ 'btn-primary': view === 'list', 'btn-outline-primary': view !== 'list' }"
                            class="btn">
                        <i class="bi bi-list-ul me-2"></i> Liste
                    </button>
                    <button type="button"
                            @click="view = 'calendar'; setTimeout(() => calendar.render(), 100)"
                            :class="{ 'btn-primary': view === 'calendar', 'btn-outline-primary': view !== 'calendar' }"
                            class="btn">
                        <i class="bi bi-calendar3 me-2"></i> Calendrier
                    </button>
                </div>
            </div>

            <!-- List View -->
            <div x-show="view === 'list'" class="shadow-sm card">
                <div class="card-body">
                    @if($tasks->isEmpty())
                        <p class="text-center text-muted">Aucune tâche assignée pour le moment.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tâche</th>
                                        <th>Équipes</th>
                                        <th>Début</th>
                                        <th>Durée</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr style="cursor: pointer;" onclick="window.location='{{ route('employee.tasks.show', $task) }}'">
                                            <td class="fw-bold">
                                                <a href="{{ route('employee.tasks.show', $task) }}" class="text-decoration-none text-dark">
                                                    {{ $task->name }}
                                                </a>
                                            </td>
                                            <td>
                                                @foreach($task->teams as $team)
                                                    <span class="badge bg-info text-dark me-1">
                                                        {{ $team->name }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                {{ $task->start_at ? $task->start_at->format('d/m/Y H:i') : '-' }}
                                            </td>
                                            <td>
                                                {{ $task->expected_minutes }} min
                                            </td>
                                            <td class="text-muted">
                                                {{ Str::limit($task->description, 50) }}
                                            </td>
                                            <td onclick="event.stopPropagation()">
                                                @if($task->completed_at)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Terminée
                                                    </span>
                                                @else
                                                    <form action="{{ route('tasks.completion.complete', $task) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Marquer comme terminée">
                                                            <i class="bi bi-magic"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Calendar View -->
            <div x-show="view === 'calendar'" class="shadow-sm card" style="display: none;">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        var calendar;
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'fr',
                firstDay: 1,
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                allDaySlot: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste'
                },
                events: @json($events),
                eventDidMount: function(info) {
                    if(info.event.extendedProps.description) {
                        info.el.title = info.event.extendedProps.description;
                    }
                }
            });
        });
    </script>
</x-app-layout>
