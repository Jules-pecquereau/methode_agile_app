<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createManager(): User
    {
        return User::factory()->create(['role' => 'manager']);
    }

    private function createUser(): User
    {
        return User::factory()->create(['role' => 'user']);
    }

    // --- TESTS D'ACCÈS ---

    public function test_non_manager_cannot_access_task_pages(): void
    {
        $user = $this->createUser();
        $task = Task::create(['name' => 'Test', 'expected_minutes' => 60, 'active' => false]);

        $this->actingAs($user)->get(route('tasks.create'))->assertForbidden();
        $this->actingAs($user)->post(route('tasks.store'))->assertForbidden();
        $this->actingAs($user)->get(route('tasks.edit', $task))->assertForbidden();
        $this->actingAs($user)->put(route('tasks.update', $task))->assertForbidden();
    }

    public function test_manager_can_access_task_pages(): void
    {
        $manager = $this->createManager();
        $this->actingAs($manager)->get(route('tasks.create'))->assertOk();
    }

    // --- TESTS DE CRÉATION ---

    public function test_manager_can_create_basic_task(): void
    {
        $manager = $this->createManager();

        $response = $this->actingAs($manager)->post(route('tasks.store'), [
            'name' => 'Nouvelle Tâche',
            'description' => 'Description de test',
            'expected_minutes' => 60,
            'active' => false,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'name' => 'Nouvelle Tâche',
            'expected_minutes' => 60,
        ]);
    }

    public function test_task_creation_validation_rules(): void
    {
        $manager = $this->createManager();

        $response = $this->actingAs($manager)->post(route('tasks.store'), [
            'name' => '',
            'expected_minutes' => 'not-integer',
        ]);

        $response->assertSessionHasErrors(['name', 'expected_minutes']);
    }

    public function test_task_becomes_active_if_teams_are_selected_on_create(): void
    {
        $manager = $this->createManager();
        $team = Team::create(['name' => 'Team Alpha']);

        $this->actingAs($manager)->post(route('tasks.store'), [
            'name' => 'Tâche Active',
            'expected_minutes' => 120,
            'active' => true,
            'teams' => [$team->id],
        ]);

        $this->assertDatabaseHas('tasks', ['name' => 'Tâche Active', 'active' => true]);
    }

    public function test_task_cannot_be_active_without_teams_on_create(): void
    {
        $manager = $this->createManager();

        $this->actingAs($manager)->post(route('tasks.store'), [
            'name' => 'Tâche Sans Équipe',
            'expected_minutes' => 120,
            'active' => true, // Demandé actif
            'teams' => [], // Pas d'équipe
        ]);

        $this->assertDatabaseHas('tasks', ['name' => 'Tâche Sans Équipe', 'active' => false]);
    }

    // --- TESTS DE MODIFICATION (NOUVEAU) ---

    public function test_manager_can_update_task(): void
    {
        $manager = $this->createManager();
        $task = Task::create(['name' => 'Vieux Nom', 'expected_minutes' => 60, 'active' => false]);

        $response = $this->actingAs($manager)->put(route('tasks.update', $task), [
            'name' => 'Nouveau Nom',
            'expected_minutes' => 120,
            'active' => false,
            'teams' => [],
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'Nouveau Nom']);
    }

    public function test_task_becomes_inactive_if_teams_removed_on_update(): void
    {
        $manager = $this->createManager();
        $team = Team::create(['name' => 'Team A']);

        // Créer une tâche active avec une équipe
        $task = Task::create(['name' => 'Task', 'expected_minutes' => 60, 'active' => true]);
        $task->teams()->attach($team);

        // Mise à jour : On garde active=true, mais on retire les équipes
        $this->actingAs($manager)->put(route('tasks.update', $task), [
            'name' => 'Task',
            'expected_minutes' => 60,
            'active' => true,
            'teams' => [], // Plus d'équipe
        ]);

        // La tâche doit être passée inactive automatiquement
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'active' => false]);
    }

    // --- TESTS DE DÉSACTIVATION ---

    public function test_manager_can_deactivate_task(): void
    {
        $manager = $this->createManager();
        $task = Task::create(['name' => 'Tâche à désactiver', 'expected_minutes' => 60, 'active' => true]);

        $this->actingAs($manager)->patch(route('tasks.deactivate', $task))
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'active' => false]);
    }
}
