<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Mail\TaskCompletedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TaskCompletionController extends Controller
{
    /**
     * Affiche les tâches planifiées non complétées
     */
    public function index(): View
    {
        $tasks = Task::whereNotNull('start_at')
            ->where('active', true)
            ->whereNull('completed_at')
            ->with('teams')
            ->get();

        return view('tasks.completion.index', compact('tasks'));
    }

    /**
     * Marque une tâche comme terminée
     */
    public function complete(Task $task): RedirectResponse
    {
        $task->update([
            'completed_at' => now()
        ]);

        // Recharger la tâche avec les relations
        $task->refresh();
        $task->load('teams');

        // Récupérer tous les managers
        $managers = User::where('role', 'manager')->get();

        // Envoyer un email à chaque manager
        foreach ($managers as $manager) {
            Mail::to($manager->email)->send(
                new TaskCompletedNotification($task, Auth::user())
            );
        }

        return back()->with('success', 'Tâche marquée comme terminée ! Les managers ont été notifiés par email.');
    }

    /**
     * Réactive une tâche terminée
     */
    public function uncomplete(Task $task): RedirectResponse
    {
        $task->update([
            'completed_at' => null
        ]);

        return back()->with('success', 'Tâche réactivée !');
    }

    /**
     * Affiche l'historique des tâches terminées
     */
    public function history(): View
    {
        $tasks = Task::whereNotNull('completed_at')
            ->with('teams')
            ->orderBy('completed_at', 'desc')
            ->paginate(15);

        return view('tasks.completion.history', compact('tasks'));
    }
}
