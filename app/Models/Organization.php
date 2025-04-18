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

    protected $fillable = ['short_name', 'name', 'nds', 'inn', 'terminal'];


    public function documentSelectionRules()
    {
        return $this->hasMany(DocumentSelectionRule::class, 'organization_id');
    }
}
