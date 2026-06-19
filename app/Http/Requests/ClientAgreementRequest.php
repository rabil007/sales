<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ClientAgreementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled(['start_date', 'duration_days'])) {
            $start = Carbon::parse($this->input('start_date'));
            $duration = (int) $this->input('duration_days');

            $this->merge([
                'end_date' => $start->copy()->addDays(max($duration - 1, 0))->toDateString(),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'agreement_ref' => [
                'required',
                'string',
                'max:255',
                Rule::unique('client_agreements', 'agreement_ref')->ignore($this->route('client_agreement')),
            ],
            'scope_of_work' => ['nullable', 'string'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'monthly_invoice_value' => ['required', 'numeric', 'min:0'],
        ];
    }
}
