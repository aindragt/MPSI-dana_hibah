<?php

namespace Tests\Feature\Pengaju;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_pengaju_can_update_organization_profile_and_upload_files_to_private_disk(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $pengaju = User::factory()->create([
            'role_id' => 1,
            'email_verified_at' => now(),
        ]);

        $foto = UploadedFile::fake()->image('logo.jpg');
        $akta = UploadedFile::fake()->create('akta.pdf', 100, 'application/pdf');

        $response = $this->actingAs($pengaju)->post(route('pengaju.profile.update'), [
            'organization_name' => 'Yayasan Danau Hibah',
            'address' => 'Jl. Merdeka No. 45',
            'district' => 'Cibinong',
            'village' => 'Pabuaran',
            'field_of_activity' => 'Keagamaan',
            'chairman_name' => 'Ahmad',
            'secretary_name' => 'Budi',
            'treasurer_name' => 'Cici',
            'organization_phone' => '08123456789',
            'organization_email' => 'contact@yayasan.org',
            'foto_profil' => $foto,
            'file_akta' => $akta,
        ]);

        $response->assertRedirect();

        // 1. Cek data tersimpan di organization_profiles
        $this->assertDatabaseHas('organization_profiles', [
            'user_id' => $pengaju->id,
            'organization_name' => 'Yayasan Danau Hibah',
            'address' => 'Jl. Merdeka No. 45',
            'district' => 'Cibinong',
            'village' => 'Pabuaran',
            'field_of_activity' => 'Keagamaan',
        ]);

        // 2. Cek file tersimpan di disk local (private)
        $pengaju->refresh();
        $this->assertNotNull($pengaju->foto_profil);
        $this->assertNotNull($pengaju->file_akta);

        Storage::disk('local')->assertExists($pengaju->foto_profil);
        Storage::disk('local')->assertExists($pengaju->file_akta);

        // 3. Pastikan file TIDAK ada di disk public
        Storage::disk('public')->assertMissing($pengaju->foto_profil);
        Storage::disk('public')->assertMissing($pengaju->file_akta);
    }
}
