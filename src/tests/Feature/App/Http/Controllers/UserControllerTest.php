<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    // index

    public function testIndex_WhenUnauthenticated_ShouldRedirectToLogin(): void
    {
        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('login.index'));
    }

    public function testIndex_WhenAuthenticatedAsNonSuperAdmin_ShouldReturn403(): void
    {
        $user = User::factory()->create(['super_admin' => false]);

        $response = $this->actingAs($user)->get(route('users.index'));

        $response->assertStatus(403);
    }

    // store

    public function testStore_WhenUnauthenticated_ShouldRedirectToLogin(): void
    {
        $response = $this->post(route('users.store'), [
            'name' => 'Test', 'email' => 'test@test.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login.index'));
    }

    public function testStore_WhenAuthenticatedAsNonSuperAdmin_ShouldReturn403(): void
    {
        $user = User::factory()->create(['super_admin' => false]);

        $response = $this->actingAs($user)->post(route('users.store'), [
            'name' => 'Test', 'email' => 'test@test.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(403);
    }

    public function testStore_WhenValidData_ShouldCreateUser(): void
    {
        $admin = User::factory()->create(['super_admin' => true]);

        $this->actingAs($admin)->post(route('users.store'), [
            'name'                  => 'New User',
            'email'                 => 'newuser@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com', 'name' => 'New User']);
    }

    public function testStore_WhenValidData_ShouldRedirectWithSuccess(): void
    {
        $admin = User::factory()->create(['super_admin' => true]);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name'                  => 'New User',
            'email'                 => 'newuser@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');
    }

    public function testStore_WhenEmailAlreadyExists_ShouldReturnValidationError(): void
    {
        $admin = User::factory()->create(['super_admin' => true, 'email' => 'existing@test.com']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name'                  => 'Other User',
            'email'                 => 'existing@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function testStore_WhenPasswordTooShort_ShouldReturnValidationError(): void
    {
        $admin = User::factory()->create(['super_admin' => true]);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name'                  => 'Test',
            'email'                 => 'test@test.com',
            'password'              => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function testStore_WhenPasswordNotConfirmed_ShouldReturnValidationError(): void
    {
        $admin = User::factory()->create(['super_admin' => true]);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name'                  => 'Test',
            'email'                 => 'test@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // destroy

    public function testDestroy_WhenUnauthenticated_ShouldRedirectToLogin(): void
    {
        $target = User::factory()->create();

        $response = $this->delete(route('users.destroy', $target));

        $response->assertRedirect(route('login.index'));
    }

    public function testDestroy_WhenAuthenticatedAsNonSuperAdmin_ShouldReturn403(): void
    {
        $user   = User::factory()->create(['super_admin' => false]);
        $target = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('users.destroy', $target));

        $response->assertStatus(403);
    }

    public function testDestroy_WhenAuthenticatedAsSuperAdmin_ShouldDeleteUser(): void
    {
        $admin  = User::factory()->create(['super_admin' => true]);
        $target = User::factory()->create();

        $this->actingAs($admin)->delete(route('users.destroy', $target));

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function testDestroy_WhenAuthenticatedAsSuperAdmin_ShouldRedirectWithSuccess(): void
    {
        $admin  = User::factory()->create(['super_admin' => true]);
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->delete(route('users.destroy', $target));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');
    }
}
