<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationServiceDocumentTemplateTable extends Migration
{
    public function up()
    {
        Schema::create('organization_service_document_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->onDelete('cascade');

            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->onDelete('cascade');

            $table->foreignId('document_template_id')
                ->constrained('document_templates', 'id')
                ->onDelete('cascade')
                ->index('org_serv_doc_template_doc_template_id_fk');

            $table->string('type');
            $table->timestamps();

            $table->unique(['organization_id', 'service_id', 'document_template_id'], 'org_serv_doc_template_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('organization_service_document_template');
    }
}
