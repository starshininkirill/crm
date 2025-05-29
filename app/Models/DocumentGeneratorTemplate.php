<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentGeneratorTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'template_id',
        'file',
    ];
    
    public $timestamps = false;
}
