<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeTaskController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Récupérer les IDs des équipes de l'utilisateur
        $teamIds = $user->teams->pluck('id');

        // Récupérer les tâches associées à ces équipes
        $tasks = Task::whereHas('teams', function ($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->with(['teams' => function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        }])->get();

        // Préparer les événements pour le calendrier
        $events = [];
        foreach ($tasks as $task) {
            foreach ($task->getTimeSegments() as $segment) {
                $events[] = [
                    'title' => $task->name,
                    'start' => $segment['start']->toIso8601String(),
                    'end' => $segment['end']->toIso8601String(),
                    'description' => $task->description,
                    'url' => route('employee.tasks.show', $task),
                    'extendedProps' => [
                        'description' => $task->description
                    ]
                ];
            }
        }

        return view('employee.tasks.index', compact('tasks', 'events'));
    }

    public function show(Task $task): View
    {
        // Vérifier si l'utilisateur a le droit de voir cette tâche (si elle appartient à une de ses équipes)
        $user = Auth::user();
        $teamIds = $user->teams->pluck('id');
        
        $hasAccess = $task->teams()->whereIn('teams.id', $teamIds)->exists();
        
        if (!$hasAccess) {
            abort(403, 'Vous n\'avez pas accès à cette tâche.');
        }

        return view('employee.tasks.show', compact('task'));
    }
}
