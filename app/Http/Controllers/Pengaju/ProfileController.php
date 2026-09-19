<?php

namespace App\Http\Controllers\Pengaju;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengaju\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $user = auth()->user()->load('organizationProfile');

        return Inertia::render('Pengaju/Profile/Edit', [
            'user' => $user,
            'organizationProfile' => $user->organizationProfile,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // 1. Update/Create organization_profile
        $orgData = [
            'organization_name' => $validated['organization_name'],
            'address' => $validated['address'],
            'district' => $validated['district'] ?? null,
            'village' => $validated['village'] ?? null,
            'field_of_activity' => $validated['field_of_activity'] ?? null,
            'chairman_name' => $validated['chairman_name'] ?? null,
            'secretary_name' => $validated['secretary_name'] ?? null,
            'treasurer_name' => $validated['treasurer_name'] ?? null,
            'organization_phone' => $validated['organization_phone'] ?? null,
            'organization_email' => $validated['organization_email'] ?? null,
        ];

        $user->organizationProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $orgData
        );

        // 2. Handle file uploads to private local disk: profiles/{user_id}/
        $userUpdates = [];
        $fileFields = ['foto_profil', 'file_akta', 'file_kesbangpol', 'rekening_lembaga', 'npwp_lembaga'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store("profiles/{$user->id}", 'local');
                $userUpdates[$field] = $path;
            }
        }

        if (! empty($userUpdates)) {
            $user->update($userUpdates);
        }

        return redirect()->back()->with('message', 'Profil organisasi berhasil diperbarui.');
    }
}
