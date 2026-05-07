<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->string('contact_person')->nullable()->after('company');
            $table->string('contact_designation')->nullable()->after('contact_person');
            $table->string('address')->nullable()->after('contact_designation');
            $table->string('city')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->dropColumn(['contact_person', 'contact_designation', 'address', 'city']);
        });
    }
};
