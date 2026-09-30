<?php

namespace Tests\Feature\Proposal;

use App\Models\DocumentType;
use App\Models\Proposal;
use App\Models\ProposalDocument;
use App\Models\SubmissionWindow;
use App\Models\User;
use App\States\ProposalStatus\Draft;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalSubmitTest extends TestCase
{
    use RefreshDatabase;

    protected User $pengaju;

    protected SubmissionWindow $activeWindow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(DocumentTypeSeeder::class);

        $this->pengaju = User::factory()->create([
            'role_id' => 1,
            'email_verified_at' => now(),
        ]);

        $this->activeWindow = SubmissionWindow::create([
            'year' => 2026,
            'open_date' => now()->subDay()->toDateString(),
            'close_date' => now()->addDays(30)->toDateString(),
            'is_active' => true,
        ]);
    }

    public function test_proposal_submission_fails_if_documents_are_incomplete(): void
    {
        $proposal = Proposal::create([
            'proposal_number' => 'HIBAH-2026-TEST1',
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $this->activeWindow->id,
            'activity_title' => 'Kegiatan Test Incomplete',
            'total_budget' => 10000000,
            'status' => Draft::class,
        ]);

        // Upload only 5 document types out of 11
        $docTypes = DocumentType::take(5)->get();
        foreach ($docTypes as $docType) {
            ProposalDocument::create([
                'proposal_id' => $proposal->id,
                'document_type_id' => $docType->id,
                'file_path' => 'proposals/2026/'.$this->pengaju->id.'/test.pdf',
                'original_filename' => 'test.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 1024,
                'version' => 1,
            ]);
        }

        $response = $this->actingAs($this->pengaju)
            ->post(route('pengaju.proposals.submit', $proposal->id));

        $response->assertStatus(403);
        $this->assertEquals('draft', (string) $proposal->fresh()->status);
    }

    public function test_proposal_submission_succeeds_when_all_11_documents_are_uploaded(): void
    {
        $proposal = Proposal::create([
            'proposal_number' => 'HIBAH-2026-TEST2',
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $this->activeWindow->id,
            'activity_title' => 'Kegiatan Test Complete',
            'total_budget' => 15000000,
            'status' => Draft::class,
        ]);

        // Upload all 11 document types
        $docTypes = DocumentType::all();
        foreach ($docTypes as $docType) {
            ProposalDocument::create([
                'proposal_id' => $proposal->id,
                'document_type_id' => $docType->id,
                'file_path' => 'proposals/2026/'.$this->pengaju->id.'/test.pdf',
                'original_filename' => 'test.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 1024,
                'version' => 1,
            ]);
        }

        $response = $this->actingAs($this->pengaju)
            ->post(route('pengaju.proposals.submit', $proposal->id));

        $response->assertRedirect(route('pengaju.proposals.show', $proposal->id));

        $proposal = $proposal->fresh();
        $this->assertEquals('diajukan', (string) $proposal->status);
        $this->assertNotNull($proposal->submitted_at);

        // Verify status log was created by observer
        $this->assertDatabaseHas('proposal_status_logs', [
            'proposal_id' => $proposal->id,
            'from_status' => 'draft',
            'to_status' => 'diajukan',
            'changed_by' => $this->pengaju->id,
        ]);
    }
}
