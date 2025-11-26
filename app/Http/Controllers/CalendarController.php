<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        // if (!Auth::check() || Auth::user()->role !== 'manager') {
        //     abort(403, 'Accès réservé aux managers.');
        // }

        $teams = Team::with('tasks')->get();

        // Préparer une liste plate de toutes les tâches assignées aux équipes
        $schedulableTasks = [];
        foreach ($teams as $team) {
            foreach ($team->tasks as $task) {
                // On affiche toutes les tâches pour le moment pour déboguer
                // if ($task->active) {
                    $schedulableTasks[] = [
                        'team_id' => $team->id,
                        'task_id' => $task->id,
                        'label' => $task->name . ' (' . $team->name . ') - ' . $task->expected_minutes . ' min' . ($task->active ? '' : ' [Inactif]'),
                        'minutes' => $task->expected_minutes
                    ];
                // }
            }
        }

        $events = [];

        foreach ($teams as $team) {
            foreach ($team->tasks as $task) {
                if ($task->pivot->start_date && $task->pivot->end_date) {
                    $events[] = [
                        'title' => $team->name . ' : ' . $task->name,
                        'start' => \Carbon\Carbon::parse($task->pivot->start_date)->toIso8601String(),
                        'end' => \Carbon\Carbon::parse($task->pivot->end_date)->toIso8601String(),
                        'allDay' => false,
                    ];
                }
            }
        }

        return view('calendar.index', compact('events', 'schedulableTasks'));
    }

    public function schedule(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'task_id' => 'required|exists:tasks,id',
            'start_date' => 'required|date',
            'start_time' => 'required',
        ]);

        $team = Team::findOrFail($validated['team_id']);
        $task = $team->tasks()->findOrFail($validated['task_id']);

        $start = \Carbon\Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
        $end = $start->copy()->addMinutes($task->expected_minutes);

        // Debug log
        \Illuminate\Support\Facades\Log::info('Scheduling task', [
            'team_id' => $team->id,
            'task_id' => $task->id,
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString()
        ]);

        $result = $team->tasks()->updateExistingPivot($task->id, [
            'start_date' => $start->toDateTimeString(),
            'end_date' => $end->toDateTimeString(),
        ]);
        
        \Illuminate\Support\Facades\Log::info('Update result: ' . $result);

        return redirect()->route('calendar')->with('success', 'Tâche planifiée avec succès.');
    }
}
