<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = Task::with('users')->latest();

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('active', true)->whereNull('completed_at');
            } elseif ($request->status === 'inactive') {
                $query->where('active', false)->whereNull('completed_at');
            } elseif ($request->status === 'completed') {
                $query->whereNotNull('completed_at');
            }
        }

        $tasks = $query->paginate(15);

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $users = \App\Models\User::where('role', '!=', 'manager')->orderBy('name')->get();

        return view('tasks.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
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
            'user_id' => 'required|exists:users,id',
            'active' => 'boolean',
        ]);

        $user = \App\Models\User::with('tasks')->findOrFail($validated['user_id']);

        // Vérification des conflits de planning
        if (!empty($validated['start_at'])) {
            $newTask = new Task([
                'start_at' => $validated['start_at'],
                'expected_minutes' => $validated['expected_minutes'],
            ]);
            $newSegments = $newTask->getTimeSegments();

            // Récupérer les tâches actives de l'utilisateur qui ont une date de début
            $existingTasks = $user->tasks()
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
                                'start_at' => "Conflit de planning pour le salarié {$user->name}. Il est déjà occupé par la tâche '{$existingTask->name}' sur cette période."
                            ])->withInput();
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

        $task->users()->attach($validated['user_id']);

        $wantsActive = $request->boolean('active');
        $task->active = $wantsActive && $task->canBeActive();
        $task->save();

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche créée avec succès et assignée au salarié.');
    }

    public function edit(Task $task): View
    {
        $users = \App\Models\User::where('role', '!=', 'manager')->orderBy('name')->get();

        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task): RedirectResponse
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
            'user_id' => 'required|exists:users,id',
            'active' => 'boolean',
        ]);

        $user = \App\Models\User::with('tasks')->findOrFail($validated['user_id']);

        // Vérification des conflits de planning
        if (!empty($validated['start_at'])) {
            // On simule la tâche avec les nouvelles valeurs pour calculer les segments
            $tempTask = new Task([
                'start_at' => $validated['start_at'],
                'expected_minutes' => $validated['expected_minutes'],
            ]);
            $newSegments = $tempTask->getTimeSegments();

            // Récupérer les tâches actives de l'utilisateur qui ont une date de début
            // EXCLURE la tâche actuelle ($task->id)
            $existingTasks = $user->tasks()
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
                                'start_at' => "Conflit de planning pour le salarié {$user->name}. Il est déjà occupé par la tâche '{$existingTask->name}' sur cette période."
                            ])->withInput();
                        }
                    }
                }
            }
        }

        $task->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'expected_minutes' => $validated['expected_minutes'],
            'start_at' => $validated['start_at'] ?? null,
        ]);

        $task->users()->sync([$validated['user_id']]);

        $task->refresh();

        $wantsActive = $request->boolean('active');
        $task->active = $wantsActive && $task->canBeActive();
        $task->save();

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche mise à jour avec succès.');
    }

    public function deactivate(Task $task): RedirectResponse
    {
        $task->update(['active' => false]);

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche désactivée avec succès.');
    }
}
