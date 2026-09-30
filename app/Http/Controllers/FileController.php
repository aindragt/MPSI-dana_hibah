<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ProposalDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Serve proposal document file from private local disk.
     */
    public function showProposalDocument(Request $request, ProposalDocument $document): StreamedResponse
    {
        $this->authorize('download', $document);

        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $document->file_path,
            $document->original_filename
        );
    }

    /**
     * Serve profile legal/photo file from private local disk.
     */
    public function showProfileFile(Request $request, User $user, string $field): StreamedResponse
    {
        // Allow user to view their own profile file or Admin Kesra to view any pengaju profile file
        if ($request->user()->role->slug !== 'admin-kesra' && $request->user()->id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $allowedFields = [
            'foto_profil',
            'file_akta',
            'file_kesbangpol',
            'rekening_lembaga',
            'npwp_lembaga',
        ];

        if (! in_array($field, $allowedFields)) {
            abort(400, 'Field file tidak valid.');
        }

        $filePath = $user->{$field};

        if (! $filePath || ! Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        $filename = basename($filePath);

        return Storage::disk('local')->download($filePath, $filename);
    }

    /**
     * Serve LPJ file from private local disk.
     */
    public function showLpj(Request $request, Proposal $proposal): StreamedResponse
    {
        if ($request->user()->role->slug !== 'admin-kesra' && $request->user()->id !== $proposal->user_id) {
            abort(403, 'Akses ditolak.');
        }

        if (! $proposal->lpj_file || ! Storage::disk('local')->exists($proposal->lpj_file)) {
            abort(404, 'File LPJ tidak ditemukan.');
        }

        $filename = basename($proposal->lpj_file);

        return Storage::disk('local')->download($proposal->lpj_file, $filename);
    }
}
