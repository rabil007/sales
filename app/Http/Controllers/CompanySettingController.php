<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCompanySettingsRequest;
use App\Models\CompanySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompanySettingController extends Controller
{
    public function edit(): View
    {
        $this->authorize('manageSettings', CompanySetting::query()->make());

        $settings = CompanySetting::query()
            ->orderBy('group')
            ->orderBy('id')
            ->get()
            ->groupBy('group');

        return view('pages.settings.company', ['settings' => $settings]);
    }

    public function update(UpdateCompanySettingsRequest $request): RedirectResponse
    {
        $settingsInput = $request->validated('settings', []);

        if ($request->hasFile('app_logo')) {
            $currentLogoPath = CompanySetting::get('app_logo_path', 'overseas-marine logo.png');
            $uploadDirectory = public_path('uploads/app-branding');
            File::ensureDirectoryExists($uploadDirectory);

            $uploadedFile = $request->file('app_logo');
            $extension = strtolower($uploadedFile->extension() ?: 'png');
            $newFileName = Str::uuid()->toString().'.'.$extension;
            $uploadedFile->move($uploadDirectory, $newFileName);
            $newLogoPath = 'app-branding/'.$newFileName;

            if ($currentLogoPath !== '' && $currentLogoPath !== 'overseas-marine logo.png' && Storage::disk('public')->exists($currentLogoPath)) {
                Storage::disk('public')->delete($currentLogoPath);
            }
            if ($currentLogoPath !== '' && $currentLogoPath !== 'overseas-marine logo.png' && str_starts_with($currentLogoPath, 'app-branding/')) {
                $previousPublicUpload = public_path('uploads/'.$currentLogoPath);
                if (is_file($previousPublicUpload)) {
                    @unlink($previousPublicUpload);
                }
            }

            $settingsInput['app_logo_path'] = $newLogoPath;
        }

        foreach ($settingsInput as $key => $value) {
            if (! in_array($key, CompanySetting::MANAGEABLE_SETTING_KEYS, true)) {
                continue;
            }

            if ($key === 'app_logo_path' && ! $request->hasFile('app_logo')) {
                continue;
            }

            CompanySetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'label' => match ($key) {
                        'app_name' => 'Application Name',
                        'app_logo_path' => 'Application Logo Path',
                        default => str($key)->replace('_', ' ')->title()->toString(),
                    },
                    'group' => match ($key) {
                        'app_name', 'app_logo_path' => 'application',
                        'company_name', 'company_legal_name' => 'identity',
                        'company_address', 'company_phone', 'company_email', 'company_website' => 'contact',
                        'signatory_name', 'signatory_role' => 'signatory',
                        'accom_single_rate', 'accom_double_rate', 'accom_events_rate' => 'accommodation',
                        'transport_rate_1', 'transport_rate_2', 'transport_rate_3', 'transport_rate_4', 'transport_rate_5' => 'transportation',
                        default => 'general',
                    },
                    'value' => (string) $value,
                ],
            );
        }

        return back()->with('status', 'Company settings saved.');
    }
}
