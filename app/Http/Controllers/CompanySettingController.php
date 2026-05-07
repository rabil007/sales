<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanySettingController extends Controller
{
    public function edit(): View
    {
        $settings = CompanySetting::query()
            ->orderBy('group')
            ->orderBy('id')
            ->get()
            ->groupBy('group');

        return view('pages.settings.company', ['settings' => $settings]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:500'],
            'app_logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('app_logo')) {
            $currentLogoPath = CompanySetting::get('app_logo_path', 'overseas-marine logo.png');
            $newLogoPath = $request->file('app_logo')->store('app-branding', 'public');

            if ($currentLogoPath !== '' && $currentLogoPath !== 'overseas-marine logo.png' && Storage::disk('public')->exists($currentLogoPath)) {
                Storage::disk('public')->delete($currentLogoPath);
            }

            $data['settings']['app_logo_path'] = $newLogoPath;
        }

        foreach ($data['settings'] as $key => $value) {
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
                        default => 'general',
                    },
                    'value' => (string) $value,
                ],
            );
        }

        return back()->with('status', 'Company settings saved.');
    }
}
