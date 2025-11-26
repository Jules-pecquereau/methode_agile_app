<x-app-layout>
    <x-slot name="header">
        <h2 class="mb-0 h4 font-weight-bold text-dark">
            {{ __('Calendrier des équipes') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container">
            <div class="row">
                <!-- Calendrier (Pleine largeur maintenant) -->
                <div class="col-md-12">
                    @if(session('success'))
                        <div class="mb-3 alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="shadow-sm card">
                        <div class="p-0 card-body">
                            <div id='calendar'></div>
                        </div>
                    </div>
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
                firstDay: 1,
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                allDaySlot: false,
                slotEventOverlap: false,
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste'
                },
                events: @json($events),
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault(); // Empêche le navigateur de suivre le lien immédiatement
                    }
                }
            });
            calendar.render();
        });
    </script>
    <style>
        .fc-event {
            cursor: pointer;
        }
        .fc-toolbar-title {
            font-size: 1.25rem !important;
        }
    </style>
</x-app-layout>
