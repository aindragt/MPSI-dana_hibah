<?php

namespace Tests\Feature\Proposal;

use App\Models\Proposal;
use App\Models\Role;
use App\Models\SubmissionWindow;
use App\Models\User;
use App\States\ProposalStatus\Draft;
use App\States\ProposalStatus\VerifikasiFinal;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalPolicyTest extends TestCase
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
        ]);

        $this->adminKesra = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $this->activeWindow = SubmissionWindow::create([
            'year' => (int) date('Y'),
            'open_date' => now()->subDays(5)->toDateString(),
            'close_date' => now()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);
    }

    public function test_pengaju_can_create_proposal_when_window_is_active_and_has_no_active_proposal(): void
    {
        $this->assertTrue($this->pengaju->can('create', Proposal::class));
    }

    public function test_pengaju_cannot_create_proposal_when_window_is_inactive_or_expired(): void
    {
        $this->activeWindow->update(['is_active' => false]);

        $this->assertFalse($this->pengaju->can('create', Proposal::class));
    }

    public function test_pengaju_cannot_create_proposal_if_already_has_active_proposal_in_same_window(): void
    {
        Proposal::create([
            'proposal_number' => 'HIBAH-TEST-01',
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $this->activeWindow->id,
            'activity_title' => 'Kegiatan 1',
            'total_budget' => 5000000,
            'status' => Draft::class,
        ]);

        $this->assertFalse($this->pengaju->can('create', Proposal::class));
    }

    public function test_pengaju_can_create_proposal_if_previous_proposal_in_window_is_terminal(): void
    {
        Proposal::create([
            'proposal_number' => 'HIBAH-TEST-02',
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $this->activeWindow->id,
            'activity_title' => 'Kegiatan Selesai',
            'total_budget' => 5000000,
            'status' => VerifikasiFinal::class,
        ]);

        $this->assertTrue($this->pengaju->can('create', Proposal::class));
    }

    public function test_pengaju_can_only_update_editable_proposal(): void
    {
        $proposal = Proposal::create([
            'proposal_number' => 'HIBAH-TEST-03',
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $this->activeWindow->id,
            'activity_title' => 'Kegiatan Draft',
            'total_budget' => 5000000,
            'status' => Draft::class,
        ]);

        $this->assertTrue($this->pengaju->can('update', $proposal));

        $proposal->status = VerifikasiFinal::class;
        $proposal->save();

        $this->assertFalse($this->pengaju->can('update', $proposal->fresh()));
    }

    public function test_admin_kesra_can_view_all_proposals(): void
    {
        $proposal = Proposal::create([
            'proposal_number' => 'HIBAH-TEST-04',
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $this->activeWindow->id,
            'activity_title' => 'Kegiatan Test',
            'total_budget' => 5000000,
            'status' => Draft::class,
        ]);

        $this->assertTrue($this->adminKesra->can('view', $proposal));

        $otherPengaju = User::factory()->create([
            'role_id' => Role::where('slug', 'pengaju')->first()->id,
        ]);
        $this->assertFalse($otherPengaju->can('view', $proposal));
    }
}
