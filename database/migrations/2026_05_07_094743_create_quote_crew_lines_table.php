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
        Schema::create('quote_crew_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id');
            $table->string('rank')->nullable();
            $table->string('category')->default('Marine');
            $table->integer('qty')->default(1);
            $table->string('basis')->default('Day');
            $table->decimal('rate', 10, 2)->default(0.00);
            $table->integer('duration')->default(0);
            $table->decimal('ot_rate', 10, 2)->nullable()->default(0.00);
            $table->date('mob_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_crew_lines');
    }
};
