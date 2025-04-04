<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'file', 'organization_id',];
    public $timestamps = false;

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function documentSelectionRules()
    {
        return $this->hasMany(DocumentSelectionRule::class, 'document_template_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'document_selection_rule_services', 'document_template_id', 'service_id');
    }
}
