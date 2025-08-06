<?php

namespace App\Models\Finance;

use App\Models\UserManagement\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryReport extends Model
{
    use HasFactory;

    const TYPE_PROJECTS_DEPARTMENT = 'projects_department';

    
    protected $fillable = [
        'department_id',
        'type',
        'period',
        'data'
    ];
    
    protected $casts = [
        'data' => 'array',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
