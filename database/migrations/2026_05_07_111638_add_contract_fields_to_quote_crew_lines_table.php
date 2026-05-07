<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quote_crew_lines', function (Blueprint $table) {
            $table->unsignedInteger('duration_days')->nullable()->after('duration');
            $table->unsignedInteger('duration_months')->nullable()->after('duration_days');
            $table->decimal('monthly_rate', 10, 2)->nullable()->after('rate');
            $table->decimal('manual_total', 12, 2)->nullable()->after('monthly_rate');
            $table->decimal('line_total', 12, 2)->default(0.00)->after('remarks');
            $table->date('demob_date')->nullable()->after('mob_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_crew_lines', function (Blueprint $table) {
            $table->dropColumn([
                'duration_days',
                'duration_months',
                'monthly_rate',
                'manual_total',
                'line_total',
                'demob_date',
            ]);
        });
    }
};
