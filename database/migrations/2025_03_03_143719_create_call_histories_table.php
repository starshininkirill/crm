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
        Schema::create('call_histories', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('phone');
            $table->string('client_phone');
            $table->integer('conversation_duration');
            $table->enum('type', ['income', 'outcome']);
            $table->foreign('phone')->references('phone')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls_histories');
    }
};
