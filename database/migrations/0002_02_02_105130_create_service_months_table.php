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
        Schema::create('service_months', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 12, 2);
            $table->foreignId('contract_id');
            $table->foreignId('tarif_id')->nullable();
            $table->foreignId('payment_id')->nullable();
            $table->integer('month');
            $table->date('payment_date')->nullable();
            $table->date('start_service_date');
            $table->date('end_service_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_months');
    }
};
