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
            ['key' => 'company_name',        'label' => 'Company Name',                    'group' => 'identity',       'value' => 'Overseas Marine Services'],
            ['key' => 'company_legal_name',  'label' => 'Legal Name',                      'group' => 'identity',       'value' => 'Overseas Marine Services-Sole Proprietorship LLC'],

            // Contact details (shown in PDF footer)
            ['key' => 'company_address',     'label' => 'Office Address',                  'group' => 'contact',        'value' => 'Office 402, Centro Capital Centre Offices Building, Al Zumurrud St, ADNEC Area, Abu Dhabi'],
            ['key' => 'company_phone',       'label' => 'Phone',                           'group' => 'contact',        'value' => '+971 2 6714722'],
            ['key' => 'company_email',       'label' => 'Email',                           'group' => 'contact',        'value' => 'info@overseas-ms.com'],
            ['key' => 'company_website',     'label' => 'Website',                         'group' => 'contact',        'value' => 'www.overseas-ms.com'],

            // Signatory (shown in PDF sign-off on page 2)
            ['key' => 'signatory_name',      'label' => 'Signatory Name',                  'group' => 'signatory',      'value' => 'Kiron V.'],
            ['key' => 'signatory_role',      'label' => 'Signatory Role',                  'group' => 'signatory',      'value' => 'Commercial Manager'],

            // Annexure II — Accommodation rates (AED)
            ['key' => 'accom_single_rate',   'label' => 'Single Room Rate (AED)',          'group' => 'accommodation',  'value' => 'xx.00'],
            ['key' => 'accom_double_rate',   'label' => 'Double Room Rate (AED)',          'group' => 'accommodation',  'value' => 'xx.00'],
            ['key' => 'accom_events_rate',   'label' => 'Special Events Supplement (AED)', 'group' => 'accommodation',  'value' => 'xx.00'],

            // Annexure II — Transportation rates (AED per trip)
            ['key' => 'transport_rate_1',    'label' => 'City → Free Port (within 5 KM)',  'group' => 'transportation', 'value' => 'xx.00'],
            ['key' => 'transport_rate_2',    'label' => 'City → Airport / Bateen Airport', 'group' => 'transportation', 'value' => 'xx.00'],
            ['key' => 'transport_rate_3',    'label' => 'ADNEC / Hotel → City Limits',     'group' => 'transportation', 'value' => 'xx.00'],
            ['key' => 'transport_rate_4',    'label' => 'City → Musaffah',                 'group' => 'transportation', 'value' => 'xx.00'],
            ['key' => 'transport_rate_5',    'label' => 'City → Dubai Airport / City',     'group' => 'transportation', 'value' => 'xxx.00'],
        ];

        foreach ($settings as $setting) {
            CompanySetting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }
    }
}
