<?php

namespace App\Http\Requests\Pengaju;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Otorisasi dilakukan di Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'activity_title' => ['required', 'string', 'max:255'],
            'activity_description' => ['nullable', 'string'],
            'total_budget' => ['required', 'numeric', 'min:0'],
            'execution_start_date' => ['nullable', 'date'],
            'execution_end_date' => ['nullable', 'date', 'after_or_equal:execution_start_date'],
        ];
    }
}
