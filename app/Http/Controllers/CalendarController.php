<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        // Récupérer les tâches qui ont une date de début
        $tasks = Task::whereNotNull('start_at')
            ->where('active', true)
            ->with('teams')
            ->get();

        $events = collect();

        foreach ($tasks as $task) {
            $teamId = $task->teams->first()->id ?? 0;
            $color = $this->getTeamColor($teamId);

            foreach ($task->getTimeSegments() as $segment) {
                $events->push([
                    'id' => $task->id,
                    'title' => $task->name . ($task->teams->isNotEmpty() ? ' (' . $task->teams->pluck('name')->join(', ') . ')' : ''),
                    'start' => $segment['start']->toIso8601String(),
                    'end' => $segment['end']->toIso8601String(),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'allDay' => false,
                    'url' => route('tasks.edit', ['task' => $task->id, 'from' => 'calendar']),
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
