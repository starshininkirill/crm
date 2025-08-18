<?php

namespace App\Models\Finance;

use App\Models\Documents\DocumentSelectionRule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    public $timestamps = false;

    public const WITHOUT_NDS = 0;
    public const WITH_NDS = 1;

    protected $fillable = [
        'short_name',
        'name',
        'nds',
        'inn',
        'terminal',
        'wiki_id',
        'has_doc_number',
        'doc_number'
    ];


    public function documentSelectionRules()
    {
        return $this->hasMany(DocumentSelectionRule::class, 'organization_id');
    }
}
