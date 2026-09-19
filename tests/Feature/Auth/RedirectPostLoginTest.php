<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectPostLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_root_url_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_redirects_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_pengaju_login_redirects_to_pengaju_dashboard(): void
    {
        $user = User::factory()->create([
            'role_id' => 1, // pengaju
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/pengaju/dashboard');
    }

    public function test_admin_kesra_login_redirects_to_admin_kesra_dashboard(): void
    {
        $user = User::factory()->create([
            'role_id' => 2, // admin-kesra
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin-kesra/dashboard');
    }

    public function test_super_admin_login_redirects_to_super_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role_id' => 3, // super_admin
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/super-admin/dashboard');
    }
}
