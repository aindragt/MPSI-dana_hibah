<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CheckRoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        Route::get('/test-pengaju-route', function () {
            return response('OK');
        })->middleware(['web', 'auth', 'role:pengaju']);

        Route::get('/test-admin-kesra-route', function () {
            return response('OK');
        })->middleware(['web', 'auth', 'role:admin-kesra']);
    }

    public function test_user_with_pengaju_role_can_access_pengaju_route(): void
    {
        $user = User::factory()->create(['role_id' => 1]); // pengaju

        $response = $this->actingAs($user)->get('/test-pengaju-route');

        $response->assertStatus(200);
    }

    public function test_user_without_pengaju_role_is_blocked_from_pengaju_route(): void
    {
        $user = User::factory()->create(['role_id' => 2]); // admin-kesra

        $response = $this->actingAs($user)->get('/test-pengaju-route');

        $response->assertStatus(403);
    }

    public function test_user_with_admin_kesra_role_can_access_admin_kesra_route(): void
    {
        $user = User::factory()->create(['role_id' => 2]); // admin-kesra

        $response = $this->actingAs($user)->get('/test-admin-kesra-route');

        $response->assertStatus(200);
    }

    public function test_user_without_admin_kesra_role_is_blocked_from_admin_kesra_route(): void
    {
        $user = User::factory()->create(['role_id' => 1]); // pengaju

        $response = $this->actingAs($user)->get('/test-admin-kesra-route');

        $response->assertStatus(403);
    }
}
