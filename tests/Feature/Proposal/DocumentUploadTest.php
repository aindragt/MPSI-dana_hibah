<?php

namespace Tests\Feature\Proposal;

use App\Models\DocumentType;
use App\Models\Proposal;
use App\Models\ProposalDocument;
use App\Models\Role;
use App\Models\SubmissionWindow;
use App\Models\User;
use App\States\ProposalStatus\Diajukan;
use App\States\ProposalStatus\Draft;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $pengaju;

    protected User $otherPengaju;

    protected Proposal $proposal;

    protected DocumentType $docType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(DocumentTypeSeeder::class);

        Storage::fake('local');
        Storage::fake('public');

        $pengajuRole = Role::where('slug', 'pengaju')->first();

        $this->pengaju = User::factory()->create([
            'role_id' => $pengajuRole->id,
            'email_verified_at' => now(),
        ]);

        $this->otherPengaju = User::factory()->create([
            'role_id' => $pengajuRole->id,
            'email_verified_at' => now(),
        ]);

        $window = SubmissionWindow::create([
            'year' => (int) date('Y'),
            'open_date' => now()->subDays(5)->toDateString(),
            'close_date' => now()->addDays(5)->toDateString(),
            'is_active' => true,
        ]);

        $this->proposal = Proposal::create([
            'proposal_number' => 'HIBAH-UPLOAD-001',
            'user_id' => $this->pengaju->id,
            'submission_window_id' => $window->id,
            'activity_title' => 'Kegiatan Test Upload',
            'total_budget' => 10000000,
            'status' => Draft::class,
        ]);

        $this->docType = DocumentType::where('slug', 'rab')->first();
    }

    public function test_pengaju_can_upload_proposal_document_successfully(): void
    {
        $file = UploadedFile::fake()->create('rab_lembaga.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->pengaju)
            ->post(route('pengaju.proposals.documents.store', $this->proposal->id), [
                'document_type_id' => $this->docType->id,
                'document' => $file,
            ]);

        $response->assertRedirect();

        $doc = ProposalDocument::where('proposal_id', $this->proposal->id)
            ->where('document_type_id', $this->docType->id)
            ->first();

        $this->assertNotNull($doc);
        $this->assertEquals(1, $doc->version);
        $this->assertEquals('rab_lembaga.pdf', $doc->original_filename);

        // Verify stored in local private disk and missing in public disk
        Storage::disk('local')->assertExists($doc->file_path);
        Storage::disk('public')->assertMissing($doc->file_path);
    }

    public function test_document_over_5mb_is_rejected(): void
    {
        // 6 MB file (6144 KB)
        $file = UploadedFile::fake()->create('large_file.pdf', 6144, 'application/pdf');

        $response = $this->actingAs($this->pengaju)
            ->post(route('pengaju.proposals.documents.store', $this->proposal->id), [
                'document_type_id' => $this->docType->id,
                'document' => $file,
            ]);

        $response->assertSessionHasErrors(['document']);
        $this->assertDatabaseCount('proposal_documents', 0);
    }

    public function test_reuploading_same_document_creates_new_version_and_keeps_old_file(): void
    {
        // Upload Version 1
        $file1 = UploadedFile::fake()->create('rab_v1.pdf', 500, 'application/pdf');
        $this->actingAs($this->pengaju)
            ->post(route('pengaju.proposals.documents.store', $this->proposal->id), [
                'document_type_id' => $this->docType->id,
                'document' => $file1,
            ]);

        $doc1 = ProposalDocument::where('proposal_id', $this->proposal->id)
            ->where('version', 1)
            ->first();

        // Upload Version 2
        $file2 = UploadedFile::fake()->create('rab_v2.pdf', 600, 'application/pdf');
        $this->actingAs($this->pengaju)
            ->post(route('pengaju.proposals.documents.store', $this->proposal->id), [
                'document_type_id' => $this->docType->id,
                'document' => $file2,
            ]);

        $doc2 = ProposalDocument::where('proposal_id', $this->proposal->id)
            ->where('version', 2)
            ->first();

        $this->assertNotNull($doc1);
        $this->assertNotNull($doc2);
        $this->assertNotEquals($doc1->file_path, $doc2->file_path);

        // Both files MUST still exist in storage (Rule 04 / DEC-08)
        Storage::disk('local')->assertExists($doc1->file_path);
        Storage::disk('local')->assertExists($doc2->file_path);
    }

    public function test_cannot_upload_document_when_proposal_is_not_editable(): void
    {
        $this->proposal->status = Diajukan::class;
        $this->proposal->save();

        $file = UploadedFile::fake()->create('rab.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->pengaju)
            ->post(route('pengaju.proposals.documents.store', $this->proposal->id), [
                'document_type_id' => $this->docType->id,
                'document' => $file,
            ]);

        $response->assertStatus(403);
    }
}
