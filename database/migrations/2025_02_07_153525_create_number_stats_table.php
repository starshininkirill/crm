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
        Schema::create('call_stats', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->date('date');
            $table->integer('income')->default(0);
            $table->integer('outcome')->default(0);
            $table->integer('duration')->default(0);
            $table->timestamps();
            $table->foreign('phone')->references('phone')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['phone', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_stats');
    }
};
