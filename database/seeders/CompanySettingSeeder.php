<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Company identity
            ['key' => 'company_name',        'label' => 'Company Name',          'group' => 'identity',   'value' => 'Overseas Marine Services'],
            ['key' => 'company_legal_name',  'label' => 'Legal Name',            'group' => 'identity',   'value' => 'Overseas Marine Services-Sole Proprietorship LLC'],

            // Contact details (shown in PDF footer)
            ['key' => 'company_address',     'label' => 'Office Address',        'group' => 'contact',    'value' => 'Office 402, Centro Capital Centre Offices Building, Al Zumurrud St, ADNEC Area, Abu Dhabi'],
            ['key' => 'company_phone',       'label' => 'Phone',                 'group' => 'contact',    'value' => '+971 2 6714722'],
            ['key' => 'company_email',       'label' => 'Email',                 'group' => 'contact',    'value' => 'info@overseas-ms.com'],
            ['key' => 'company_website',     'label' => 'Website',               'group' => 'contact',    'value' => 'www.overseas-ms.com'],

            // Signatory (shown in PDF sign-off on page 2)
            ['key' => 'signatory_name',      'label' => 'Signatory Name',        'group' => 'signatory',  'value' => 'Kiron V.'],
            ['key' => 'signatory_role',      'label' => 'Signatory Role',        'group' => 'signatory',  'value' => 'Commercial Manager'],
        ];

        foreach ($settings as $setting) {
            CompanySetting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }
    }
}
