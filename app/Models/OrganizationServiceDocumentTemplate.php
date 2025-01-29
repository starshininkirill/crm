<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationServiceDocumentTemplate extends Model
{
    protected $table = 'organization_service_document_template';

    protected $fillable = [
        'organization_id',
        'service_id',
        'document_template_id',
        'type',
    ];

    const PHYSIC_DEFAULT = 'physic_default';
    const PHYSIC_COMPLEX = 'physic_complex';
    const LAW_DEFAULT = 'law_default';
    const LAW_COMPLEX = 'law_complex';
    

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function documentTemplate()
    {
        return $this->belongsTo(DocumentTemplate::class);
    }
}
