<?php

namespace App\Http\Requests;

use App\Models\CompanySetting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCompanySettingsRequest extends FormRequest
{
    /** @var list<string> */
    private const DISALLOWED_KEYS = ['app_logo_path'];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->can('manageSettings', CompanySetting::query()->make());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'settings' => ['present', 'array'],
            'settings.*' => ['nullable', 'string', 'max:2000'],
            'app_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $allowedForForm = array_values(array_diff(
                CompanySetting::MANAGEABLE_SETTING_KEYS,
                self::DISALLOWED_KEYS,
            ));

            foreach (array_keys((array) $this->input('settings', [])) as $key) {
                if (! in_array($key, $allowedForForm, true)) {
                    $validator->errors()->add(
                        'settings',
                        __('Unknown or disallowed setting key: :key', ['key' => $key]),
                    );
                }
            }
        });
    }
}
