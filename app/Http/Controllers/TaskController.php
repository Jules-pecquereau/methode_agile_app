<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\Request;

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
            'teams' => 'array',
            'teams.*' => 'exists:teams,id',
            'active' => 'boolean',
        ]);

        $task = Task::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'expected_minutes' => $validated['expected_minutes'],
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
            'teams' => 'array',
            'teams.*' => 'exists:teams,id',
            'active' => 'boolean',
        ]);

        // Mise à jour des champs principaux (cela met à jour updated_at automatiquement)
        $task->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'expected_minutes' => $validated['expected_minutes'],
        ]);

        // Synchronisation des équipes
        $task->teams()->sync($validated['teams'] ?? []);

        // Recharger les relations
        $task->refresh();

        // Mise à jour du statut actif
        $wantsActive = $request->boolean('active');
        $task->active = $wantsActive && $task->canBeActive();
        $task->save(); // Force la mise à jour de updated_at

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
