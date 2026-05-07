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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->string('type');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('status')->default('Draft');
            $table->string('currency')->default('AED');
            $table->string('client_name');
            $table->string('client_po')->nullable();
            $table->string('vessel')->nullable();
            $table->string('location')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('payment_terms')->nullable();
            $table->text('scope')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
