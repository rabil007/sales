<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
            'doc_no' => ['required', 'string', 'max:255', Rule::unique('invoices', 'doc_no')->ignore($this->route('invoice'))],
            'quote_id' => ['nullable', 'integer', 'exists:quotes,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_po' => ['nullable', 'string', 'max:255'],
            'vessel' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'status' => ['required', Rule::in(['Draft', 'Issued', 'Paid', 'Overdue', 'Cancelled'])],
            'currency' => ['required', Rule::in(['AED', 'USD', 'EUR'])],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
            'payment_instructions' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.description' => ['required_with:items', 'string', 'max:255'],
            'items.*.category' => ['nullable', 'string', 'max:255'],
            'items.*.qty' => ['required_with:items', 'integer', 'min:1'],
            'items.*.basis' => ['required_with:items', 'string', 'max:50'],
            'items.*.rate' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.duration' => ['nullable', 'numeric', 'min:0'],
            'items.*.duration_unit' => ['nullable', 'string', 'max:50'],
        ];
    }
}
