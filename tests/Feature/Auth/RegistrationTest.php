<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_with_institutional_fields(): void
    {
        $response = $this->post('/register', [
            'name' => 'Yayasan Peduli Kasih',
            'email' => 'test@example.com',
            'nama_ketua' => 'Budi Santoso',
            'no_wa' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 45 Pelalawan',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(1, $user->role_id);
        $this->assertNull($user->created_by);
        $this->assertEquals('Budi Santoso', $user->nama_ketua);
        $this->assertEquals('081234567890', $user->no_wa);
        $this->assertEquals('Jl. Merdeka No. 45 Pelalawan', $user->alamat);
    }
}
