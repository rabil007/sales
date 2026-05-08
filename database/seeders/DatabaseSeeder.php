<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Quote;
use App\Models\QuoteCrewLine;
use App\Models\Rank;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        QuoteCrewLine::query()->delete();
        Quote::query()->delete();
        Client::query()->delete();
        Rank::query()->delete();
        CompanySetting::query()->delete();
        User::query()->where('email', '!=', 'admin@sales.test')->delete();

        $this->call(CompanySettingSeeder::class);

        User::query()->updateOrCreate(
            ['email' => 'admin@sales.test'],
            ['name' => 'Sales Admin', 'password' => Hash::make('password')],
        );

        $clients = collect([
            [
                'name' => 'ADNOC Offshore',
                'email' => 'procurement@adnoc-offshore.test',
                'phone' => '+97126714722',
                'company' => 'ADNOC Offshore',
                'contact_person' => 'Vimal Kumar',
                'contact_designation' => 'Crewing Supervisor',
                'address' => 'Office # 304, 3rd Floor, Al Salmeen Golden Tower, Zayed First Street',
                'city' => 'Abu Dhabi, UAE',
            ],
            [
                'name' => 'DP World',
                'email' => 'contracts@dpworld.test',
                'phone' => '+97142000000',
                'company' => 'DP World',
                'contact_person' => 'Rayan Mathew',
                'contact_designation' => 'Contracts Lead',
                'address' => 'JAFZA View 19 Tower, Jebel Ali',
                'city' => 'Dubai, UAE',
            ],
            [
                'name' => 'Mubadala Energy',
                'email' => 'vendor.marine@mubadala-energy.test',
                'phone' => '+97126950000',
                'company' => 'Mubadala Energy',
                'contact_person' => 'Sahar Al Mansoori',
                'contact_designation' => 'Supply Chain Specialist',
                'address' => 'Mubadala HQ, Al Maryah Island',
                'city' => 'Abu Dhabi, UAE',
            ],
        ])->mapWithKeys(fn (array $client) => [
            $client['name'] => Client::query()->create($client),
        ]);

        collect([
            ['name' => 'Master', 'category' => 'Marine', 'default_basis' => 'Day', 'default_rate' => 980.00, 'is_active' => true],
            ['name' => 'Chief Officer', 'category' => 'Marine', 'default_basis' => 'Day', 'default_rate' => 760.00, 'is_active' => true],
            ['name' => 'AB Seaman', 'category' => 'Marine', 'default_basis' => 'Day', 'default_rate' => 320.00, 'is_active' => true],
            ['name' => 'Cook', 'category' => 'Catering', 'default_basis' => 'Month', 'default_rate' => 6800.00, 'is_active' => true],
            ['name' => 'Rigger', 'category' => 'Deck', 'default_basis' => 'Day', 'default_rate' => 350.00, 'is_active' => true],
            ['name' => 'Crane Operator', 'category' => 'Operations', 'default_basis' => 'Day', 'default_rate' => 540.00, 'is_active' => false],
        ])->each(fn (array $rank) => Rank::query()->create($rank));

        $quote1 = Quote::query()->create([
            'doc_no' => 'OMS-Q-2026-001',
            'type' => 'Proposal',
            'issue_date' => now()->subDays(12)->toDateString(),
            'expiry_date' => now()->addDays(18)->toDateString(),
            'status' => 'Sent',
            'currency' => 'AED',
            'client_id' => $clients['ADNOC Offshore']->id,
            'client_name' => 'ADNOC Offshore',
            'client_po' => 'PO-ADNOC-4401',
            'vessel' => 'Barge 14',
            'location' => 'Abu Dhabi',
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(32)->toDateString(),
            'duration_text' => '30 days',
            'project_name' => 'Melody Crew Requirements',
            'payment_terms' => '30 days from invoice',
            'scope' => 'Source and supply of deck and bridge crew under OMS payroll and client secondment model.',
            'terms_conditions' => 'Rates are VAT exclusive. Monthly invoices are due before the 20th of each month.',
            'special_conditions' => 'Client to provide LOA for gate pass processing.',
            'terms' => ['mobilization' => 'Included', 'demobilization' => 'Included'],
            'total_amount' => 49600.00,
        ]);

        $quote1->crewLines()->createMany([
            [
                'rank' => 'Master',
                'category' => 'Marine',
                'qty' => 1,
                'basis' => 'Day',
                'rate' => 980.00,
                'monthly_rate' => null,
                'duration' => 30,
                'duration_days' => 30,
                'duration_months' => null,
                'manual_total' => null,
                'ot_rate' => 80.00,
                'mob_date' => now()->addDays(2)->toDateString(),
                'demob_date' => now()->addDays(32)->toDateString(),
                'remarks' => 'Bridge command duty',
                'line_total' => 29400.00,
            ],
            [
                'rank' => 'AB Seaman',
                'category' => 'Marine',
                'qty' => 2,
                'basis' => 'Day',
                'rate' => 340.00,
                'monthly_rate' => null,
                'duration' => 30,
                'duration_days' => 30,
                'duration_months' => null,
                'manual_total' => null,
                'ot_rate' => 30.00,
                'mob_date' => now()->addDays(2)->toDateString(),
                'demob_date' => now()->addDays(32)->toDateString(),
                'remarks' => 'Deck watchkeeping',
                'line_total' => 20400.00,
            ],
        ]);

        $quote2 = Quote::query()->create([
            'doc_no' => 'OMS-Q-2026-002',
            'type' => 'Rate Contract',
            'issue_date' => now()->subDays(40)->toDateString(),
            'expiry_date' => now()->addDays(320)->toDateString(),
            'status' => 'Active',
            'currency' => 'AED',
            'client_id' => $clients['DP World']->id,
            'client_name' => 'DP World',
            'client_po' => 'DPW-RC-2208',
            'vessel' => 'Terminal Operations',
            'location' => 'Jebel Ali',
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays(360)->toDateString(),
            'duration_text' => '12 months',
            'project_name' => 'Port Crew Rotation',
            'payment_terms' => '45 days from invoice',
            'scope' => 'Monthly crew supply for marine terminal support, transport arranged by client.',
            'terms_conditions' => 'Client validates attendance sheet by month-end.',
            'special_conditions' => null,
            'terms' => ['insurance' => 'Supplier scope', 'visa' => 'Supplier scope'],
            'total_amount' => 47600.00,
        ]);

        $quote2->crewLines()->createMany([
            [
                'rank' => 'Cook',
                'category' => 'Catering',
                'qty' => 1,
                'basis' => 'Month',
                'rate' => 0,
                'monthly_rate' => 6800.00,
                'duration' => 6,
                'duration_days' => null,
                'duration_months' => 6,
                'manual_total' => null,
                'ot_rate' => 0,
                'mob_date' => now()->subDays(5)->toDateString(),
                'demob_date' => now()->addMonths(6)->toDateString(),
                'remarks' => 'Galley and mess support',
                'line_total' => 40800.00,
            ],
            [
                'rank' => 'Rigger',
                'category' => 'Deck',
                'qty' => 1,
                'basis' => 'Fixed',
                'rate' => 0,
                'monthly_rate' => null,
                'duration' => 1,
                'duration_days' => null,
                'duration_months' => null,
                'manual_total' => 6800.00,
                'ot_rate' => 0,
                'mob_date' => now()->subDays(5)->toDateString(),
                'demob_date' => now()->addMonths(1)->toDateString(),
                'remarks' => 'Shutdown window support',
                'line_total' => 6800.00,
            ],
        ]);

        Quote::query()->create([
            'doc_no' => 'OMS-Q-2026-003',
            'type' => 'Proposal',
            'issue_date' => now()->subDays(5)->toDateString(),
            'expiry_date' => now()->addDays(25)->toDateString(),
            'status' => 'Draft',
            'currency' => 'AED',
            'client_id' => $clients['Mubadala Energy']->id,
            'client_name' => 'Mubadala Energy',
            'client_po' => null,
            'vessel' => 'Jack-Up Unit',
            'location' => 'Mubarraz',
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(70)->toDateString(),
            'duration_text' => '60 days',
            'project_name' => 'Offshore Campaign Crew',
            'payment_terms' => '30 days from invoice',
            'scope' => 'Crew mobilization proposal pending final line-up.',
            'terms_conditions' => null,
            'special_conditions' => null,
            'terms' => ['status' => 'Awaiting client confirmation'],
            'total_amount' => 0,
        ]);
    }
}
