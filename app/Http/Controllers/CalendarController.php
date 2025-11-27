<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        // Récupérer les équipes avec leurs utilisateurs et les tâches de ces utilisateurs
        $teams = Team::with(['users.tasks' => function($query) {
            $query->where('active', true)->whereNotNull('start_at');
        }])->get();

        $events = collect();

        foreach ($teams as $team) {
            $teamSegments = collect();

            // Collecter tous les segments de temps de tous les utilisateurs de l'équipe
            foreach ($team->users as $user) {
                foreach ($user->tasks as $task) {
                    $isCompleted = !is_null($task->pivot->completed_at);
                    foreach ($task->getTimeSegments() as $segment) {
                        $teamSegments->push([
                            'start' => $segment['start'],
                            'end' => $segment['end'],
                            'is_completed' => $isCompleted,
                        ]);
                    }
                }
            }

            // Trier les segments par date de début
            $teamSegments = $teamSegments->sortBy('start');

            // Fusionner les segments qui se chevauchent ou se touchent
            $mergedSegments = collect();
            if ($teamSegments->isNotEmpty()) {
                $currentStart = $teamSegments->first()['start'];
                $currentEnd = $teamSegments->first()['end'];
                $currentIsCompleted = $teamSegments->first()['is_completed'];

                foreach ($teamSegments->slice(1) as $segment) {
                    if ($segment['start']->lte($currentEnd)) {
                        // Chevauchement ou contiguïté, on étend la fin si nécessaire
                        if ($segment['end']->gt($currentEnd)) {
                            $currentEnd = $segment['end'];
                        }
                        // Si un seul segment fusionné n'est pas terminé, le bloc entier n'est pas terminé
                        if (!$segment['is_completed']) {
                            $currentIsCompleted = false;
                        }
                    } else {
                        // Trou, on enregistre le segment courant et on en commence un nouveau
                        $mergedSegments->push([
                            'start' => $currentStart, 
                            'end' => $currentEnd,
                            'is_completed' => $currentIsCompleted
                        ]);
                        $currentStart = $segment['start'];
                        $currentEnd = $segment['end'];
                        $currentIsCompleted = $segment['is_completed'];
                    }
                }
                // Enregistrer le dernier segment
                $mergedSegments->push([
                    'start' => $currentStart, 
                    'end' => $currentEnd,
                    'is_completed' => $currentIsCompleted
                ]);
            }

            // Créer les événements pour le calendrier
            $color = $this->getTeamColor($team->id);
            foreach ($mergedSegments as $segment) {
                $events->push([
                    'title' => $team->name . ($segment['is_completed'] ? ' (Terminé)' : ''),
                    'start' => $segment['start']->toIso8601String(),
                    'end' => $segment['end']->toIso8601String(),
                    'backgroundColor' => $segment['is_completed'] ? '#198754' : $color, // Vert si terminé
                    'borderColor' => $segment['is_completed'] ? '#198754' : $color,
                    'allDay' => false,
                ]);
            }
        }

        return view('calendar.index', compact('events'));
    }

    private function getTeamColor($teamId)
    {
        $colors = ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0'];
        return $colors[$teamId % count($colors)];
    }
}
