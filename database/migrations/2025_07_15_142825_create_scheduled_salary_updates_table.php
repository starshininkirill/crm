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
        Schema::create('scheduled_updates', function (Blueprint $table) {
            $table->id();
            $table->morphs('updatable');
            $table->integer('new_value');
            $table->string('status')->default('pending');
            $table->date('effective_date');
            $table->string('field');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_updates');
    }
};
