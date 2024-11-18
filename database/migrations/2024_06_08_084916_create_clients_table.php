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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->integer('tax');
            
            $table->string('fio')->nullable();
            $table->integer('passport_series')->nullable();
            $table->integer('passport_number')->nullable();
            $table->string('passport_issued')->nullable();
            $table->string('physical_address')->nullable();

            $table->string('organization_name')->nullable();
            $table->string('organization_short_name')->nullable();
            $table->integer('register_number_type')->nullable();
            $table->bigInteger('register_number')->nullable();
            $table->string('director_name')->nullable();
            $table->string('legal_address')->nullable();
            $table->bigInteger('inn')->nullable();
            $table->bigInteger('current_account')->nullable();
            $table->bigInteger('correspondent_account')->nullable();
            $table->string('bank_name')->nullable();
            $table->bigInteger('bank_bik')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};