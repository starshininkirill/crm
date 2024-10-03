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
        Schema::create('work_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->float('goal')->nullable();
            $table->integer('mounth')->nullable();
            $table->float('bonus')->nullable();
            $table->foreignId('service_category_id')->nullable()->constrained('service_categories')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('position_id')->nullable()->constrained('positions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_plans');
    }
};
