<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CriteriaAlternative extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "criteria_alternative";
    protected $fillable = [
        'alternative_id',
        'criteria_id',
        'value',
    ];

    public function alternative()
    {
        return $this->belongsTo(Alternative::class, 'alternative_id', 'id');
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id', 'id');
    }
}
