<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasFilter;

class DocumentGeneratorTemplate extends Model
{
    use HasFactory, HasFilter;

    protected $fillable = [
        'result_name',
        'template_id',
        'use_custom_doc_number',
        'file',
    ];

    public $timestamps = false;
}
