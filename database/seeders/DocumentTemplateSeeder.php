<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentTemplate::create([
            'name' => 'ИП 1 Счёт + Акт',
            'file' => '/storage/documents/СчетАктИП1Версия2.docx',
        ]);
        DocumentTemplate::create([
            'name' => 'ИП 2 Счёт + Акт',
            'file' => '/storage/documents/Счет Акт ИП2.docx'
        ]);
        DocumentTemplate::create([
            'name' => 'ООО Счёт + Акт',
            'file' => '/storage/documents/Счет ООО ЭДДИ ГРУПП.docx'
        ]);
    }
}
