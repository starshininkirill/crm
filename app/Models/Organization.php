<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;
    public $timestamps = false;

    public const WITHOUT_NDS = 0;
    public const WITH_NDS = 1;

    protected $fillable = ['short_name', 'name', 'nds', 'inn', 'template', 'active', 'terminal'];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'organization_service_document_template')
            ->withPivot('document_template_id', 'type');
    }

    public function documentTemplates()
    {
        return $this->belongsToMany(DocumentTemplate::class, 'organization_service_document_template')
            ->withPivot('service_id', 'type');
    }
}
