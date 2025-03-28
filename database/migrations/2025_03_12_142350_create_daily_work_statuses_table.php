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
        Schema::create('daily_work_statuses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_status_id')->constrained()->onDelete('cascade');
            $table->boolean('confirmed')->default(false);
            $table->float('hours')->nullable()->default(null);
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_work_statuses');
    }
};
