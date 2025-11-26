<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = Task::with('teams')->latest()->paginate(15);

        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $teams = Team::orderBy('name')->get();

        return view('tasks.create', compact('teams'));
    }

    public function store(Request $request): RedirectResponse
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

        if (! empty($validated['teams'])) {
            $task->teams()->sync($validated['teams']);
        }

        $wantsActive = $request->boolean('active');
        $task->active = $wantsActive && $task->canBeActive();
        $task->save();

        return redirect()->route('tasks.index')
            ->with('success', 'Tâche créée avec succès.');
    }

    public function edit(Task $task): View
    {
        $teams = Team::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'teams'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'expected_minutes' => 'required|integer|min:1',
            'teams' => 'array',
            'teams.*' => 'exists:teams,id',
            'active' => 'boolean',
        ]);

        $task->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'expected_minutes' => $validated['expected_minutes'],
        ]);

        $task->teams()->sync($validated['teams'] ?? []);

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
