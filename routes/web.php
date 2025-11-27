<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->middleware(['auth'])->name('home');

use App\Http\Controllers\EmployeeTaskController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserController::class);
    Route::get('/my-tasks', [EmployeeTaskController::class, 'index'])->name('employee.tasks.index');
    Route::get('/my-tasks/{task}', [EmployeeTaskController::class, 'show'])->name('employee.tasks.show');
});

// Route::middleware(['manager'])->group(function () {
    Route::resource('tasks', TaskController::class)->except(['destroy', 'show']);
    Route::patch('tasks/{task}/deactivate', [TaskController::class, 'deactivate'])->name('tasks.deactivate');
    Route::get('/calendar', [CalendarController::class, 'index'])->middleware(['auth'])->name('calendar.index');
    Route::post('/calendar/schedule', [CalendarController::class, 'schedule'])->name('calendar.schedule');
// });

Route::get('/debug-calendar', function () {
    $teams = App\Models\Team::with('tasks')->get();
    return $teams->map(function($team) {
        return $team->tasks->map(function($task) {
            return [
                'task_name' => $task->name,
                'pivot' => $task->pivot
            ];
        });
    });
});

Route::middleware(['auth', 'manager'])->group(function () {
    Route::resource('tasks', TaskController::class)->except(['destroy', 'show']);
    Route::patch('tasks/{task}/deactivate', [TaskController::class, 'deactivate'])->name('tasks.deactivate');
});

use App\Mail\TestEmail;

Route::get('/test-email', function () {
    Illuminate\Support\Facades\Mail::to('test@example.com')->send(new TestEmail());
    return 'Email envoyé ! Vérifiez storage/logs/laravel.log';
});

require __DIR__.'/auth.php';




