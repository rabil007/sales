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
            $table->foreign('quote_id')
                ->references('id')
                ->on('quotes')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_crew_lines', function (Blueprint $table) {
            $table->dropForeign(['quote_id']);
        });
    }
};
