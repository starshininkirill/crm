<?php

namespace App\Models;

use App\Models\Traits\HasFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedDocument extends Model
{
    use HasFactory, HasFilter;

    protected $fillable = [
        'type',
        'deal',
        'file_name',
        'word_file',
        'pdf_file',
        'act_number',
        'creater',
        'inn',
        'document_date'
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
    ];

    public function formatedType(): string
    {
        if ($this->type === null) {
            return '';
        }

        return self::ASOC_TYPES[$this->type];
    }
}
