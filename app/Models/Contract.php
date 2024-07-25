<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = ['client', 'amount_price', 'service_id'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
