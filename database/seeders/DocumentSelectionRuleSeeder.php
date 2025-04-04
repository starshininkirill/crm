<?php

namespace Database\Seeders;

use App\Models\DocumentSelectionRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSelectionRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ИП 2
        DocumentSelectionRule::create([
            'document_template_id' => 4,
            'type' => DocumentSelectionRule::TYPE_PHYSIC,
            'priority' => 1,
        ])->services()->sync(1);

        DocumentSelectionRule::create([
            'document_template_id' => 5,
            'type' => DocumentSelectionRule::TYPE_PHYSIC,
            'priority' => 1,
        ])->services()->sync(2);
        DocumentSelectionRule::create([
            'document_template_id' => 6,
            'type' => DocumentSelectionRule::TYPE_PHYSIC,
            'priority' => 1,
        ])->services()->sync(3);
        DocumentSelectionRule::create([
            'document_template_id' => 7,
            'type' => DocumentSelectionRule::TYPE_PHYSIC,
            'priority' => 1,
        ])->services()->sync(4);

        // ИП 6
        DocumentSelectionRule::create([
            'document_template_id' => 8,
            'type' => DocumentSelectionRule::TYPE_PHYSIC,
            'priority' => 2,
        ])->services()->sync(1);
        DocumentSelectionRule::create([
            'document_template_id' => 9,
            'type' => DocumentSelectionRule::TYPE_PHYSIC,
            'priority' => 2,
        ])->services()->sync(2);
        DocumentSelectionRule::create([
            'document_template_id' => 10,
            'type' => DocumentSelectionRule::TYPE_PHYSIC,
            'priority' => 2,
        ])->services()->sync(3);
        DocumentSelectionRule::create([
            'document_template_id' => 11,
            'type' => DocumentSelectionRule::TYPE_PHYSIC,
            'priority' => 2,
        ])->services()->sync(4);

        // ИП 1
        DocumentSelectionRule::create([
            'document_template_id' => 12,
            'type' => DocumentSelectionRule::TYPE_PHYSIC,
            'priority' => 1,
        ])->services()->sync(5);

        //
    }
}
