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
            'file' => 'documents/СчетАктИП1Версия2.docx',
            'organization_id' => 1,
        ]);
        DocumentTemplate::create([
            'name' => 'ИП 2 Счёт + Акт',
            'file' => 'documents/Счет Акт ИП2.docx',
            'organization_id' => 2,
        ]);
        DocumentTemplate::create([
            'name' => 'ООО Счёт + Акт',
            'file' => 'documents/Счет ООО ЭДДИ ГРУПП.docx',
            'organization_id' => 3,
        ]);

        // ИП 2
        DocumentTemplate::create([
            'name' => 'ИП_2_Физ_инд_Ленд',
            'file' => 'documents/9. Физ_инд_Ленд.docx',
            'organization_id' => 2,
        ]);
        DocumentTemplate::create([
            'name' => 'ИП_2_Физ_инд_КС',
            'file' => 'documents/10. Физ_инд_КС.docx',
            'organization_id' => 2,
        ]);
        DocumentTemplate::create([
            'name' => 'ИП_2_Физ_инд_Каталог',
            'file' => 'documents/11. Физ_инд_Каталог.docx',
            'organization_id' => 2,
        ]);
        DocumentTemplate::create([
            'name' => 'ИП_2_Физ_инд_ИМ',
            'file' => 'documents/12. Физ_инд_ИМ.docx',
            'organization_id' => 2,
        ]);

        // ИП 6
        DocumentTemplate::create([
            'name' => 'ИП_6_Зимина_Физ_инд_Ленд',
            'file' => 'documents/9. Зимина_Физ_инд_Ленд.docx',
            'organization_id' => 6,
        ]);
        DocumentTemplate::create([
            'name' => 'ИП_6_Зимина_Физ_инд_КС',
            'file' => 'documents/10. Зимина_Физ_инд_КС.docx',
            'organization_id' => 6,
        ]);
        DocumentTemplate::create([
            'name' => 'ИП_6_Зимина_Физ_инд_Каталог',
            'file' => 'documents/11. Зимина_Физ_инд_Каталог.docx',
            'organization_id' => 6,
        ]);
        DocumentTemplate::create([
            'name' => 'ИП_6_Зимина_Физ_инд_ИМ',
            'file' => 'documents/12. Зимина_Физ_инд_ИМ.docx',
            'organization_id' => 6,
        ]);

        // ИП 1
        DocumentTemplate::create([
            'name' => 'ИП_1_Физ_РК _РК_SEO',
            'file' => 'documents/18. Физ_РК _РК_SEO_1.docx',
            'organization_id' => 1,
        ]);
    }
}
