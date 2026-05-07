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
        Schema::table('quotes', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('currency')->constrained()->nullOnDelete();
            $table->string('duration_text')->nullable()->after('end_date');
            $table->string('project_name')->nullable()->after('duration_text');
            $table->text('terms_conditions')->nullable()->after('scope');
            $table->text('special_conditions')->nullable()->after('terms_conditions');
            $table->unsignedInteger('renewal_notice_days')->nullable()->after('special_conditions');
            $table->json('terms')->nullable()->after('renewal_notice_days');
            $table->date('renewed_from_expiry_date')->nullable()->after('terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
            $table->dropColumn([
                'duration_text',
                'project_name',
                'terms_conditions',
                'special_conditions',
                'renewal_notice_days',
                'terms',
                'renewed_from_expiry_date',
            ]);
        });
    }
};
