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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('number')->nullable()->constrained()->onDelete('cascade');
            $table->float('amount_price')->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('contracts')->references('id')->onDelete('set null');
            $table->foreignId('client_id');
        }); 
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
