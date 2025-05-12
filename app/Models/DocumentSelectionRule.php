<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DocumentSelectionRule extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'document_template_id',
        'type',
    ];

    const TYPE_PHYSIC = 'physic';
    const TYPE_LAW = 'law';
    const TYPE_ACT_DOCUMENT = 'act_document';

    public function documentTemplate():BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'document_selection_rule_services', 'document_template_id', 'service_id');
    }

    public static function types(): array
    {
        return [
            self::TYPE_PHYSIC,
            self::TYPE_LAW,
            self::TYPE_ACT_DOCUMENT
        ];
    }

    public static function visualTypes(): array
    {
        return [
            [
                'name' => 'Физик',
                'value' => self::TYPE_PHYSIC
            ],
            [
                'name' => 'Юрик',
                'value' => self::TYPE_LAW
            ],
            [
                'name' => 'Счёт + Акт',
                'value' => self::TYPE_ACT_DOCUMENT
            ],
        ];
    }

    public static function translateType(string $type): string
    {
        $types = [
            self::TYPE_PHYSIC => 'Физик',
            self::TYPE_LAW => 'Юрик',
            self::TYPE_ACT_DOCUMENT => 'Счёт + Акт',
        ];

        return array_key_exists($type, $types) ?  $types[$type] : '';
    }
}
