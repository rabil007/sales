<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'doc_no' => ['required', 'string', 'max:255', Rule::unique('quotes', 'doc_no')->ignore($this->route('quote'))],
            'type' => ['required', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'status' => ['required', Rule::in(['Draft', 'Sent', 'Approved', 'Active', 'Expired'])],
            'currency' => ['required', Rule::in(['AED', 'USD', 'EUR'])],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_po' => ['nullable', 'string', 'max:255'],
            'vessel' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'duration_text' => ['nullable', 'string', 'max:255'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'scope' => ['nullable', 'string'],
            'terms_conditions' => ['nullable', 'string'],
            'special_conditions' => ['nullable', 'string'],
            'terms' => ['nullable', 'array'],
            'terms.*' => ['nullable', 'string'],
            'crew_lines' => ['nullable', 'array'],
            'crew_lines.*.rank' => ['nullable', 'string', 'max:255'],
            'crew_lines.*.category' => ['required_with:crew_lines', 'string', 'max:255'],
            'crew_lines.*.qty' => ['required_with:crew_lines', 'integer', 'min:1'],
            'crew_lines.*.basis' => ['required_with:crew_lines', Rule::in(['Day', 'Month', 'Fixed'])],
            'crew_lines.*.rate' => ['required_with:crew_lines', 'numeric', 'min:0'],
            'crew_lines.*.monthly_rate' => ['nullable', 'numeric', 'min:0'],
            'crew_lines.*.duration' => ['nullable', 'integer', 'min:0'],
            'crew_lines.*.duration_days' => ['nullable', 'integer', 'min:0'],
            'crew_lines.*.duration_months' => ['nullable', 'integer', 'min:0'],
            'crew_lines.*.manual_total' => ['nullable', 'numeric', 'min:0'],
            'crew_lines.*.ot_rate' => ['nullable', 'numeric', 'min:0'],
            'crew_lines.*.mob_date' => ['nullable', 'date'],
            'crew_lines.*.demob_date' => ['nullable', 'date'],
            'crew_lines.*.remarks' => ['nullable', 'string'],
        ];
    }
}
