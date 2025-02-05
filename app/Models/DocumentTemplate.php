<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'file'];
    public $timestamps = false;

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_service_document_template')
            ->withPivot('service_id', 'type');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'organization_service_document_template')
            ->withPivot('organization_id', 'type');
    }

    public function filePath()
    {
        return str_replace('/storage/', '', $this->file);
    }
}
