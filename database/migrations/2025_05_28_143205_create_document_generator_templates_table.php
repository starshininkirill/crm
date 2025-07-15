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
        Schema::create('document_generator_templates', function (Blueprint $table) {
            $table->id();
            $table->string('result_name');
            $table->bigInteger('template_id')->unique();
            $table->boolean('use_custom_doc_number')->default(false);
            $table->string('file');
        });
    }

    /** 
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_generator_templates');
    }
};
