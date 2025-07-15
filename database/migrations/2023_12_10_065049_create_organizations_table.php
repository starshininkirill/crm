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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('short_name');
            $table->string('name');
            $table->boolean('nds')->default(false);
            $table->bigInteger('inn')->unique()->nullable();
            $table->integer('terminal')->nullable();
            $table->bigInteger('doc_number')->nullable();
            $table->boolean('has_doc_number')->default(false);
            $table->integer('wiki_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
