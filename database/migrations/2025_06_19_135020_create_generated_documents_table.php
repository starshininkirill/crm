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
        Schema::create('generated_documents', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('deal');
            $table->string('file_name');
            $table->json('data')->nullable();
            $table->string('word_file')->nullable();
            $table->string('pdf_file')->nullable();
            $table->string('act_number')->nullable();
            $table->string('creater')->nullable();
            $table->string('inn')->nullable();
            $table->date('document_date')->nullable();
            $table->string('template_id')->nullable();
            $table->foreignId('document_generator_template_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_documents');
    }
};
