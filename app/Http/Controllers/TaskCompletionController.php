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
            ->with('users')
            ->get();

        return view('tasks.completion.index', compact('tasks'));
    }

    /**
     * Marque une tâche comme terminée
     */
    public function complete(Task $task): RedirectResponse
    {
        $user = Auth::user();
        
        // Mettre à jour le pivot pour l'utilisateur courant
        $task->users()->updateExistingPivot($user->id, [
            'completed_at' => now()
        ]);

        // Vérifier si tous les utilisateurs ont terminé la tâche
        $allCompleted = true;
        foreach ($task->users as $taskUser) {
            // On doit recharger le pivot pour avoir la valeur à jour
            if ($taskUser->id === $user->id) {
                continue; // Déjà marqué comme fait
            }
            if (is_null($taskUser->pivot->completed_at)) {
                $allCompleted = false;
                break;
            }
        }

        // Si tous les utilisateurs ont terminé, on marque la tâche globale comme terminée
        if ($allCompleted) {
            $task->update(['completed_at' => now()]);
            
            // Envoyer un email aux managers seulement quand la tâche est globalement terminée
            $managers = User::where('role', 'manager')->get();
            foreach ($managers as $manager) {
                Mail::to($manager->email)->send(
                    new TaskCompletedNotification($task, $user)
                );
            }
            
            return back()->with('success', 'Tâche terminée ! Tous les membres ont fini, les managers ont été notifiés.');
        }

        return back()->with('success', 'Votre participation à la tâche est marquée comme terminée.');
    }

    /**
     * Réactive une tâche terminée
     */
    public function uncomplete(Task $task): RedirectResponse
    {
        $user = Auth::user();
        
        // Réactiver pour l'utilisateur courant
        $task->users()->updateExistingPivot($user->id, [
            'completed_at' => null
        ]);

        // Si la tâche était marquée comme globalement terminée, on la réouvre
        if ($task->completed_at) {
            $task->update(['completed_at' => null]);
        }

        return back()->with('success', 'Tâche réactivée pour vous !');
    }

    /**
     * Affiche l'historique des tâches terminées
     */
    public function history(): View
    {
        $tasks = Task::whereNotNull('completed_at')
            ->with('users')
            ->orderBy('completed_at', 'desc')
            ->paginate(15);

        return view('tasks.completion.history', compact('tasks'));
    }
}
