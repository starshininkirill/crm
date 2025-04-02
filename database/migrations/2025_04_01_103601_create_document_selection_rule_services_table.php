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
        Schema::create('document_selection_rule_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_template_id')
                ->constrained('document_templates')
                ->onDelete('cascade');
            $table->foreignId('service_id')
                ->constrained('services')
                ->onDelete('cascade');
        
            $table->index(['document_template_id', 'service_id'], 'dsr_template_service_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_selection_rule_services');
    }
};
