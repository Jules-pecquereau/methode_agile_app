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
            foreach ($task->teams as $team) {
                if ($team->pivot->start_date && $team->pivot->end_date) {
                    $events[] = [
                        'title' => $task->name . ' (' . $team->name . ')',
                        'start' => $team->pivot->start_date,
                        'end' => $team->pivot->end_date,
                        'description' => $task->description,
                        'extendedProps' => [
                            'team' => $team->name,
                            'description' => $task->description
                        ]
                    ];
                }
            }
        }

        return view('employee.tasks.index', compact('tasks', 'events'));
    }
}
