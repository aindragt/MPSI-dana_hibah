<?php

namespace App\Http\Controllers\Pengaju;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengaju\UploadDocumentRequest;
use App\Models\DocumentType;
use App\Models\Proposal;
use App\Models\ProposalDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class DocumentUploadController extends Controller
{
    /**
     * Store a newly uploaded proposal document in private storage with versioning.
     */
    public function store(UploadDocumentRequest $request, Proposal $proposal): RedirectResponse
    {
        // Otorisasi: Pengaju harus pemilik proposal dan proposal harus dalam status editable
        if ($request->user()->id !== $proposal->user_id || ! $proposal->status->isEditable()) {
            abort(403, 'Anda tidak diizinkan mengunggah dokumen untuk proposal ini.');
        }

        $validated = $request->validated();
        $docType = DocumentType::findOrFail($validated['document_type_id']);
        $file = $request->file('document');

        // Cari versi terakhir untuk (proposal_id, document_type_id)
        $latestDoc = ProposalDocument::where('proposal_id', $proposal->id)
            ->where('document_type_id', $docType->id)
            ->orderBy('version', 'desc')
            ->first();

        $nextVersion = $latestDoc ? ($latestDoc->version + 1) : 1;

        $year = $proposal->submissionWindow->year ?? date('Y');
        $userId = $proposal->user_id;
        $slug = $docType->slug;
        $ext = $file->getClientOriginalExtension();
        $random = Str::random(8);

        $targetFolder = "proposals/{$year}/{$userId}";
        $filename = "{$slug}_v{$nextVersion}_{$random}.{$ext}";

        // Store to private disk ('local')
        $path = $file->storeAs($targetFolder, $filename, 'local');

        // Create new record in proposal_documents
        ProposalDocument::create([
            'proposal_id' => $proposal->id,
            'document_type_id' => $docType->id,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'version' => $nextVersion,
        ]);

        return redirect()->back()->with('success', "Dokumen {$docType->name} versi {$nextVersion} berhasil diunggah.");
    }
}
