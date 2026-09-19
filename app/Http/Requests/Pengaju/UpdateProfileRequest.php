<?php

namespace App\Http\Requests\Pengaju;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Field profil organisasi
            'organization_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'district' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'field_of_activity' => ['nullable', 'string', 'max:255'],
            'chairman_name' => ['nullable', 'string', 'max:255'],
            'secretary_name' => ['nullable', 'string', 'max:255'],
            'treasurer_name' => ['nullable', 'string', 'max:255'],
            'organization_phone' => ['nullable', 'string', 'max:30'],
            'organization_email' => ['nullable', 'email', 'max:255'],

            // Berkas legalitas (file upload)
            'foto_profil' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'file_akta' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'file_kesbangpol' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'rekening_lembaga' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'npwp_lembaga' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
