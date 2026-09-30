<?php

namespace Tests\Feature\File;

use App\Models\DocumentType;
use App\Models\Proposal;
use App\Models\ProposalDocument;
use App\Models\Role;
use App\Models\SubmissionWindow;
use App\Models\User;
use App\States\ProposalStatus\Draft;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $pengaju;

    protected User $otherPengaju;

    protected User $adminKesra;

    protected Proposal $proposal;

    protected ProposalDocument $document;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(DocumentTypeSeeder::class);

        Storage::fake('local');

        $pengajuRole = Role::where('slug', 'pengaju')->first();
        $adminRole = Role::where('slug', 'admin-kesra')->first();

        $this->pengaju = User::factory()->create([
            'role_id' => $pengajuRole->id,
            'email_verified_at' => now(),
        ]);

        $this->otherPengaju = User::factory()->create([
            'role_id' => $pengajuRole->id,
            'email_verified_at' => now(),
        ]);

        $this->adminKesra = User::factory()->create([
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        $window = SubmissionWindow::create([
            'year' => (int) date('Y'),
            'open_date' => now()->subDays(5)->toDateString(),
            'close_date' => now()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);

        $this->proposal = Proposal::create([
            'proposal_number' => 'HIBAH-FILE-001',
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $window->id,
            'activity_title' => 'Kegiatan Test File',
            'total_budget' => 10000000,
            'status' => Draft::class,
        ]);

        $filePath = 'proposals/2026/1/rab_v1_test.pdf';
        Storage::disk('local')->put($filePath, 'dummy pdf content');

        $docType = DocumentType::first();

        $this->document = ProposalDocument::create([
            'proposal_id' => $this->proposal->id,
            'document_type_id' => $docType->id,
            'file_path' => $filePath,
            'original_filename' => 'RAB_Lembaga.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'version' => 1,
        ]);
    }

    public function test_pengaju_can_download_own_proposal_document(): void
    {
        $response = $this->actingAs($this->pengaju)
            ->get(route('files.proposal-document', $this->document->id));

        $response->assertOk();
        $response->assertHeader('content-disposition', 'attachment; filename=RAB_Lembaga.pdf');
    }

    public function test_admin_kesra_can_download_all_proposal_documents(): void
    {
        $response = $this->actingAs($this->adminKesra)
            ->get(route('files.proposal-document', $this->document->id));

        $response->assertOk();
    }

    public function test_other_user_cannot_download_proposal_document(): void
    {
        $response = $this->actingAs($this->otherPengaju)
            ->get(route('files.proposal-document', $this->document->id));

        $response->assertStatus(403);
    }

    public function test_files_are_served_from_local_private_disk(): void
    {
        // Ensure disk public does not have this file
        Storage::fake('public');

        $response = $this->actingAs($this->pengaju)
            ->get(route('files.proposal-document', $this->document->id));

        $response->assertOk();

        // Verify public disk was NOT used
        Storage::disk('public')->assertMissing($this->document->file_path);
    }
}
