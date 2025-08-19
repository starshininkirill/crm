<?php

namespace App\Models\Documents;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasFilter;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentGeneratorTemplate extends Model
{
    use HasFactory, HasFilter;

    protected $fillable = [
        'result_name',
        'template_id',
        'use_custom_doc_number',
        'file',
        'template_name',
    ];

    public $timestamps = false;

    public function generatedDocuments() : HasMany
    {
        return $this->hasMany(GeneratedDocument::class, 'document_generator_template_id');
    }
}
