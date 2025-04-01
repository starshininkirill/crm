<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSelectionRuleService extends Model
{
    use HasFactory;


    protected $fillable = [
        'document_template_id',
        'service_id',
    ];

    public function documentSelectionRule()
    {
        return $this->belongsTo(DocumentSelectionRule::class, 'document_template_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
