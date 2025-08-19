<?php

namespace App\Models\Documents;

use App\Models\Traits\HasFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedDocument extends Model
{
    use HasFactory, HasFilter;

    protected $fillable = [
        'type',
        'deal',
        'data',
        'file_name',
        'word_file',
        'pdf_file',
        'act_number',
        'creater',
        'inn',
        'document_date',
        'template_id',
        'document_generator_template_id',
    ];

    const TYPE_DEAL = 'deal';
    const TYPE_PAY = 'pay';
    const TYPE_ACT = 'act';
    const TYPE_INVOICE = 'invoice';

    const ASOC_TYPES = [
        self::TYPE_DEAL => 'Договор',
        self::TYPE_PAY => 'Счёт/Акт',
        self::TYPE_ACT => 'Акт',
        self::TYPE_INVOICE => 'Счёт/фактура',
    ];

    protected $casts = [
        'document_date' => 'date',
        'data' => 'array',
    ];

    public function template() : BelongsTo
    {
        return $this->belongsTo(DocumentGeneratorTemplate::class, 'document_generator_template_id');
    }

    public function formatedType(): string
    {
        if ($this->type === null) {
            return '';
        }

        return self::ASOC_TYPES[$this->type];
    }
}
