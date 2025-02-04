<?php

namespace Database\Seeders;

use App\Models\OrganizationServiceDocumentTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OSDTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        OrganizationServiceDocumentTemplate::create([
            'organization_id' => '1',
            'service_id' => null,
            'document_template_id' => 1,
            'type' => 'act_document',
        ]);
        OrganizationServiceDocumentTemplate::create([
            'organization_id' => '2',
            'service_id' => null,
            'document_template_id' => 2,
            'type' => 'act_document',
        ]);
        OrganizationServiceDocumentTemplate::create([
            'organization_id' => '3',
            'service_id' => null,
            'document_template_id' => 3,
            'type' => 'act_document',
        ]);
    }
}
