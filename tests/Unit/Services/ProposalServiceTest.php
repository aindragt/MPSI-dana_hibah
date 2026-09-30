<?php

namespace Tests\Unit\Services;

use App\Models\Proposal;
use App\Models\Role;
use App\Models\SubmissionWindow;
use App\Models\User;
use App\Services\ProposalService;
use App\States\ProposalStatus\Draft;
use App\States\ProposalStatus\VerifikasiFinal;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProposalService $service;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->service = new ProposalService;

        $pengajuRole = Role::where('slug', 'pengaju')->first();
        $this->user = User::factory()->create([
            'role_id' => $pengajuRole->id,
        ]);
    }

    public function test_generate_proposal_number_returns_correct_format(): void
    {
        $year = 2026;
        $number = $this->service->generateProposalNumber($year);

        $this->assertMatchesRegularExpression('/^HIBAH-2026-\d+$/', $number);
    }

    public function test_get_active_window_returns_window_when_active_and_in_range(): void
    {
        $activeWindow = SubmissionWindow::create([
            'year' => 2026,
            'open_date' => now()->subDays(2)->toDateString(),
            'close_date' => now()->addDays(2)->toDateString(),
            'is_active' => true,
        ]);

        $result = $this->service->getActiveWindow();

        $this->assertNotNull($result);
        $this->assertEquals($activeWindow->id, $result->id);
    }

    public function test_get_active_window_returns_null_when_outside_date_or_inactive(): void
    {
        SubmissionWindow::create([
            'year' => 2026,
            'open_date' => now()->subDays(10)->toDateString(),
            'close_date' => now()->subDays(2)->toDateString(),
            'is_active' => true,
        ]);

        $this->assertNull($this->service->getActiveWindow());
    }

    public function test_has_active_proposal_this_year_returns_true_when_active_proposal_exists(): void
    {
        $window = SubmissionWindow::create([
            'year' => 2026,
            'open_date' => now()->subDays(2)->toDateString(),
            'close_date' => now()->addDays(2)->toDateString(),
            'is_active' => true,
        ]);

        Proposal::create([
            'proposal_number' => 'HIBAH-2026-123456',
            'user_id' => $this->user->id,
            'submission_window_id' => $window->id,
            'activity_title' => 'Kegiatan Test',
            'total_budget' => 10000000,
            'status' => Draft::class,
        ]);

        $this->assertTrue($this->service->hasActiveProposalThisYear($this->user, 2026));
    }

    public function test_has_active_proposal_this_year_returns_false_when_proposal_is_terminal_or_none(): void
    {
        $window = SubmissionWindow::create([
            'year' => 2026,
            'open_date' => now()->subDays(2)->toDateString(),
            'close_date' => now()->addDays(2)->toDateString(),
            'is_active' => true,
        ]);

        Proposal::create([
            'proposal_number' => 'HIBAH-2026-123456',
            'user_id' => $this->user->id,
            'submission_window_id' => $window->id,
            'activity_title' => 'Kegiatan Finished',
            'total_budget' => 10000000,
            'status' => VerifikasiFinal::class,
        ]);

        $this->assertFalse($this->service->hasActiveProposalThisYear($this->user, 2026));
    }
}
