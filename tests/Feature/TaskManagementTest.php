<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createManager(): User
    {
        return User::factory()->create(["role" => "manager"]);
    }

    private function createUser(): User
    {
        return User::factory()->create(["role" => "user"]);
    }

    // --- TESTS D ACCES ---

    public function test_non_manager_cannot_access_task_pages(): void
    {
        $user = $this->createUser();
        $task = Task::create(["name" => "Test", "expected_minutes" => 60, "active" => false]);

        $this->actingAs($user)->get(route("tasks.create"))->assertForbidden();
        $this->actingAs($user)->post(route("tasks.store"))->assertForbidden();
        $this->actingAs($user)->get(route("tasks.edit", $task))->assertForbidden();
        $this->actingAs($user)->put(route("tasks.update", $task))->assertForbidden();
    }

    public function test_manager_can_access_task_pages(): void
    {
        $manager = $this->createManager();
        $this->actingAs($manager)->get(route("tasks.create"))->assertOk();
    }

    // --- TESTS DE CREATION ---

    public function test_manager_can_create_basic_task(): void
    {
        $manager = $this->createManager();
        $user = $this->createUser();

        $response = $this->actingAs($manager)->post(route("tasks.store"), [
            "name" => "Nouvelle Tache",
            "description" => "Description de test",
            "expected_minutes" => 60,
            "active" => false,
            "user_id" => $user->id,
        ]);

        $response->assertRedirect(route("tasks.index"));
        $this->assertDatabaseHas("tasks", [
            "name" => "Nouvelle Tache",
            "expected_minutes" => 60,
        ]);
        
        $task = Task::where("name", "Nouvelle Tache")->first();
        $this->assertTrue($task->users->contains($user));
    }

    public function test_task_creation_validation_rules(): void
    {
        $manager = $this->createManager();

        $response = $this->actingAs($manager)->post(route("tasks.store"), [
            "name" => "",
            "expected_minutes" => "not-integer",
            // user_id missing
        ]);

        $response->assertSessionHasErrors(["name", "expected_minutes", "user_id"]);
    }

    public function test_task_is_active_when_created_with_active_flag(): void
    {
        $manager = $this->createManager();
        $user = $this->createUser();

        $this->actingAs($manager)->post(route("tasks.store"), [
            "name" => "Tache Active",
            "expected_minutes" => 120,
            "active" => true,
            "user_id" => $user->id,
        ]);

        $this->assertDatabaseHas("tasks", ["name" => "Tache Active", "active" => true]);
    }

    // --- TESTS DE MODIFICATION ---

    public function test_manager_can_update_task(): void
    {
        $manager = $this->createManager();
        $user = $this->createUser();
        $task = Task::create(["name" => "Vieux Nom", "expected_minutes" => 60, "active" => false]);
        $task->users()->attach($user);

        $newUser = $this->createUser();

        $response = $this->actingAs($manager)->put(route("tasks.update", $task), [
            "name" => "Nouveau Nom",
            "expected_minutes" => 120,
            "active" => false,
            "user_id" => $newUser->id,
        ]);

        $response->assertRedirect(route("tasks.index"));
        $this->assertDatabaseHas("tasks", ["id" => $task->id, "name" => "Nouveau Nom"]);
        
        $task->refresh();
        $this->assertTrue($task->users->contains($newUser));
        $this->assertFalse($task->users->contains($user));
    }

    // --- TESTS DE DESACTIVATION ---

    public function test_manager_can_deactivate_task(): void
    {
        $manager = $this->createManager();
        $task = Task::create(["name" => "Tache a desactiver", "expected_minutes" => 60, "active" => true]);

        $this->actingAs($manager)->patch(route("tasks.deactivate", $task))
            ->assertRedirect(route("tasks.index"));

        $this->assertDatabaseHas("tasks", ["id" => $task->id, "active" => false]);
    }
}
