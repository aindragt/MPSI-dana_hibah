<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleRouteAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_pengaju_can_access_pengaju_dashboard(): void
    {
        $pengaju = User::factory()->create([
            'role_id' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($pengaju)->get('/pengaju/dashboard');

        $response->assertStatus(200);
    }

    public function test_admin_kesra_can_access_admin_kesra_dashboard(): void
    {
        $adminKesra = User::factory()->create([
            'role_id' => 2,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($adminKesra)->get('/admin-kesra/dashboard');

        $response->assertStatus(200);
    }

    public function test_pengaju_cannot_access_admin_kesra_dashboard(): void
    {
        $pengaju = User::factory()->create([
            'role_id' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($pengaju)->get('/admin-kesra/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_kesra_cannot_access_pengaju_dashboard(): void
    {
        $adminKesra = User::factory()->create([
            'role_id' => 2,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($adminKesra)->get('/pengaju/dashboard');

        $response->assertStatus(403);
    }
}
