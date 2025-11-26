<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('teams')->latest()->paginate(15);
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $teams = Team::orderBy('name')->get();
        return view('tasks.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'expected_minutes' => 'required|integer|min:1',
            'start_at' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && \Carbon\Carbon::parse($value)->isWeekend()) {
                        $fail('Les tâches ne peuvent pas être planifiées le week-end.');
                    }
                },
            ],
            'teams' => 'array',
            'teams.*' => 'exists:teams,id',
            'active' => 'boolean',
        ]);

        // Vérification des conflits de planning
        if (!empty($validated['start_at']) && !empty($validated['teams'])) {
            $newTask = new Task([
                'start_at' => $validated['start_at'],
                'expected_minutes' => $validated['expected_minutes'],
            ]);
            $newSegments = $newTask->getTimeSegments();

            foreach ($validated['teams'] as $teamId) {
                $team = Team::find($teamId);
                // Récupérer les tâches actives de l'équipe qui ont une date de début
                $existingTasks = $team->tasks()
                    ->where('active', true)
                    ->whereNotNull('start_at')
                    ->get();

                foreach ($existingTasks as $existingTask) {
                    foreach ($existingTask->getTimeSegments() as $existingSegment) {
                        foreach ($newSegments as $newSegment) {
                            // Vérifier le chevauchement
                            if ($newSegment['start']->lt($existingSegment['end']) &&
                                $newSegment['end']->gt($existingSegment['start'])) {

                                return back()->withErrors([
                                    'start_at' => "Conflit de planning pour l'équipe {$team->name}. Elle est déjà occupée par la tâche '{$existingTask->name}' sur cette période."
                                ])->withInput();
                            }
                        }
                    }
                }
            }
        }

        $task = Task::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'expected_minutes' => $validated['expected_minutes'],
            'start_at' => $validated['start_at'] ?? null,
            'active' => false,
        ]);

        if (!empty($validated['teams'])) {
            $task->teams()->sync($validated['teams']);
        }

        $wantsActive = $request->boolean('active');
        $task->active = $wantsActive && $task->canBeActive();
        $task->save();

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche créée avec succès.');
    }

    public function edit(Task $task)
    {
        $teams = Team::orderBy('name')->get();
        return view('tasks.edit', compact('task', 'teams'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'expected_minutes' => 'required|integer|min:1',
            'start_at' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && \Carbon\Carbon::parse($value)->isWeekend()) {
                        $fail('Les tâches ne peuvent pas être planifiées le week-end.');
                    }
                },
            ],
            'teams' => 'array',
            'teams.*' => 'exists:teams,id',
            'active' => 'boolean',
        ]);

        // Vérification des conflits de planning
        if (!empty($validated['start_at']) && !empty($validated['teams'])) {
            // On simule la tâche avec les nouvelles valeurs pour calculer les segments
            $tempTask = new Task([
                'start_at' => $validated['start_at'],
                'expected_minutes' => $validated['expected_minutes'],
            ]);
            $newSegments = $tempTask->getTimeSegments();

            foreach ($validated['teams'] as $teamId) {
                $team = Team::find($teamId);
                // Récupérer les tâches actives de l'équipe qui ont une date de début
                // EXCLURE la tâche actuelle ($task->id)
                $existingTasks = $team->tasks()
                    ->where('active', true)
                    ->whereNotNull('start_at')
                    ->where('tasks.id', '!=', $task->id)
                    ->get();

                foreach ($existingTasks as $existingTask) {
                    foreach ($existingTask->getTimeSegments() as $existingSegment) {
                        foreach ($newSegments as $newSegment) {
                            // Vérifier le chevauchement
                            if ($newSegment['start']->lt($existingSegment['end']) &&
                                $newSegment['end']->gt($existingSegment['start'])) {

                                return back()->withErrors([
                                    'start_at' => "Conflit de planning pour l'équipe {$team->name}. Elle est déjà occupée par la tâche '{$existingTask->name}' sur cette période."
                                ])->withInput();
                            }
                        }
                    }
                }
            }
        }

        // Mise à jour des champs principaux (cela met à jour updated_at automatiquement)
        $task->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'expected_minutes' => $validated['expected_minutes'],
            'start_at' => $validated['start_at'] ?? null,
        ]);

        // Synchronisation des équipes
        $task->teams()->sync($validated['teams'] ?? []);

        // Recharger les relations
        $task->refresh();

        // Mise à jour du statut actif
        $wantsActive = $request->boolean('active');
        $task->active = $wantsActive && $task->canBeActive();
        $task->save(); // Force la mise à jour de updated_at

        if ($request->input('from') === 'calendar') {
            return redirect()->route('calendar.index')
                ->with('success', 'Tâche mise à jour avec succès.');
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche mise à jour avec succès.');
    }

    public function deactivate(Task $task)
    {
        $task->update(['active' => false]); // update() met à jour updated_at automatiquement

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche désactivée avec succès.');
    }
}
