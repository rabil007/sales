<?php

namespace Database\Seeders;

use App\Models\Client;
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
        User::query()->updateOrCreate([
            'email' => 'admin@sales.test',
        ], [
            'name' => 'Sales Admin',
            'password' => Hash::make('password'),
        ]);

        Client::query()->updateOrCreate([
            'name' => 'ADNOC Offshore',
        ], [
            'email' => 'procurement@adnoc.test',
            'phone' => '+971500000001',
            'company' => 'ADNOC',
        ]);

        Client::query()->updateOrCreate([
            'name' => 'DP World',
        ], [
            'email' => 'contracts@dpworld.test',
            'phone' => '+971500000002',
            'company' => 'DP World',
        ]);
    }
}
