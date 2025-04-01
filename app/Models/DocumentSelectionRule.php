<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSelectionRule extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'organization_id',
        'document_template_id',
        'type',
    ];

    const PHYSIC_DEFAULT = 'physic_default';
    const PHYSIC_COMPLEX = 'physic_complex';
    const LAW_DEFAULT = 'law_default';
    const LAW_COMPLEX = 'law_complex';
    const ACT_DOCUMENT = 'act_document';


    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function documentTemplate()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'document_selection_rule_services', 'document_template_id', 'service_id');
    }

    public static function types(): array
    {
        return [
            self::PHYSIC_DEFAULT,
            self::PHYSIC_COMPLEX,
            self::LAW_DEFAULT,
            self::LAW_COMPLEX,
            self::ACT_DOCUMENT
        ];
    }

    public static function visualTypes(): array
    {
        return [
            [
                'name' => 'Физик одна услуга',
                'value' => self::PHYSIC_DEFAULT
            ],
            [
                'name' => 'Физик комплекс',
                'value' => self::PHYSIC_COMPLEX
            ],
            [
                'name' => 'Юрик одна услуга',
                'value' => self::LAW_DEFAULT
            ],
            [
                'name' => 'Юрик комплекс',
                'value' => self::LAW_COMPLEX
            ],
            [
                'name' => 'Счёт + Акт',
                'value' => self::ACT_DOCUMENT
            ],
        ];
    }

    public static function translateType(string $type): string
    {
        $types = [
            self::PHYSIC_DEFAULT => 'Физик одна услуга',
            self::PHYSIC_COMPLEX => 'Физик комплекс',
            self::LAW_DEFAULT => 'Юрик одна услуга',
            self::LAW_COMPLEX => 'Юрик комплекс',
            self::ACT_DOCUMENT => 'Счёт + Акт',
        ];

        return array_key_exists($type, $types) ?  $types[$type] : '';
    }
}
