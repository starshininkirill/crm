<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['value', 'contract_id', 'status', 'order'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function getFormatValue(){
        return number_format($this->value, 0, '.', ' ') . ' ₽' ;
    }
}
