<?php

namespace Tests\Feature\Proposal;

use App\Models\Proposal;
use App\Models\Role;
use App\Models\SubmissionWindow;
use App\Models\User;
use App\States\ProposalStatus\Draft;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $pengaju;

    protected User $adminKesra;

    protected SubmissionWindow $activeWindow;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(DocumentTypeSeeder::class);

        $pengajuRole = Role::where('slug', 'pengaju')->first();
        $adminRole = Role::where('slug', 'admin-kesra')->first();

        $this->pengaju = User::factory()->create([
            'role_id' => $pengajuRole->id,
            'email_verified_at' => now(),
        ]);

        $this->adminKesra = User::factory()->create([
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        $this->activeWindow = SubmissionWindow::create([
            'year' => (int) date('Y'),
            'open_date' => now()->subDays(5)->toDateString(),
            'close_date' => now()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);
    }

    public function test_pengaju_can_view_own_proposals_index(): void
    {
        Proposal::create([
            'proposal_number' => 'HIBAH-2026-001',
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $this->activeWindow->id,
            'activity_title' => 'Kegiatan 1',
            'total_budget' => 10000000,
            'status' => Draft::class,
        ]);

        $response = $this->actingAs($this->pengaju)
            ->get(route('pengaju.proposals.index'));

        $response->assertOk();
    }

    public function test_pengaju_can_store_new_proposal_when_window_is_active(): void
    {
        $payload = [
            'activity_title' => 'Pengadaan Peralatan Hibah',
            'activity_description' => 'Deskripsi kegiatan hibah',
            'total_budget' => 15000000,
            'execution_start_date' => now()->addDays(10)->toDateString(),
            'execution_end_date' => now()->addDays(20)->toDateString(),
        ];

        $response = $this->actingAs($this->pengaju)
            ->post(route('pengaju.proposals.store'), $payload);

        $response->assertRedirect(route('pengaju.proposals.index'));

        $this->assertDatabaseHas('proposals', [
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $this->activeWindow->id,
            'activity_title' => 'Pengadaan Peralatan Hibah',
            'total_budget' => 15000000,
            'status' => 'draft',
        ]);

        $proposal = Proposal::where('user_id', $this->pengaju->id)->first();
        $this->assertNotNull($proposal->proposal_number);
        $this->assertStringStartsWith('HIBAH-', $proposal->proposal_number);
    }

    public function test_pengaju_cannot_store_proposal_when_no_active_window(): void
    {
        $this->activeWindow->update(['is_active' => false]);

        $payload = [
            'activity_title' => 'Kegiatan Tanpa Jendela',
            'total_budget' => 10000000,
        ];

        $response = $this->actingAs($this->pengaju)
            ->post(route('pengaju.proposals.store'), $payload);

        $response->assertStatus(403);
    }

    public function test_pengaju_cannot_store_proposal_if_already_has_active_proposal(): void
    {
        Proposal::create([
            'proposal_number' => 'HIBAH-2026-002',
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $this->activeWindow->id,
            'activity_title' => 'Kegiatan Aktif',
            'total_budget' => 5000000,
            'status' => Draft::class,
        ]);

        $payload = [
            'activity_title' => 'Kegiatan Kedua',
            'total_budget' => 10000000,
        ];

        $response = $this->actingAs($this->pengaju)
            ->post(route('pengaju.proposals.store'), $payload);

        $response->assertStatus(403);
    }
}
