<?php

use App\Models\TimeCheck;
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
        Schema::create('time_checks', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->foreignId('user_id')->onDelete('cascade');
            $table->enum('action', TimeCheck::ACTIONS);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_checks');
    }
};
