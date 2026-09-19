<?php

namespace Tests\Feature\Auth;

use App\Models\SubmissionWindow;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_database_seeder_runs_successfully(): void
    {
        $this->assertDatabaseCount('roles', 3);
        $this->assertDatabaseCount('submission_windows', 1);
    }

    public function test_super_admin_user_can_login(): void
    {
        $superAdmin = User::where('email', 'admin@ehibah-kesra.test')->first();

        $this->assertNotNull($superAdmin);
        $this->assertEquals(3, $superAdmin->role_id);
        $this->assertTrue((bool) $superAdmin->is_active);

        $response = $this->post('/login', [
            'email' => 'admin@ehibah-kesra.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($superAdmin);
        $response->assertRedirect('/super-admin/dashboard');
    }

    public function test_submission_window_2026_exists_and_is_active(): void
    {
        $window = SubmissionWindow::where('year', 2026)->first();

        $this->assertNotNull($window);
        $this->assertEquals('2026-01-01', $window->open_date->format('Y-m-d'));
        $this->assertEquals('2026-05-31', $window->close_date->format('Y-m-d'));
        $this->assertTrue((bool) $window->is_active);
    }
}
