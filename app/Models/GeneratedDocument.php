<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedDocument extends Model
{
    use HasFactory;

    protected $fillable = ['type','deal', 'file_name', 'word_file', 'pdf_file'];

    const TYPE_DEAL = 'deal';
    const TYPE_PAY = 'pay';
}
