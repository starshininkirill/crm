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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_status_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('status')->default('pending');
            $table->float('hours')->nullable()->default(null);
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->string('links')->nullable();
            $table->text('report')->nullable();
            $table->text('description')->nullable();
            $table->string('file')->nullable();
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
