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
        Schema::create('client_agreement_crew_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_agreement_id')->constrained()->cascadeOnDelete();
            $table->string('rank')->nullable();
            $table->string('category')->default('Marine');
            $table->integer('qty')->default(1);
            $table->string('basis')->default('Day');
            $table->decimal('rate', 10, 2)->default(0.00);
            $table->decimal('monthly_rate', 10, 2)->nullable();
            $table->integer('duration')->default(0);
            $table->unsignedInteger('duration_days')->nullable();
            $table->unsignedInteger('duration_months')->nullable();
            $table->decimal('manual_total', 12, 2)->nullable();
            $table->decimal('ot_rate', 10, 2)->nullable()->default(0.00);
            $table->date('mob_date')->nullable();
            $table->date('demob_date')->nullable();
            $table->text('remarks')->nullable();
            $table->decimal('line_total', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_agreement_crew_lines');
    }
};
