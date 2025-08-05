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
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('minimal_price');
            $table->string('optimal_price');
            $table->text('description');
            $table->string('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifs');
    }
};
