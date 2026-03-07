<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubCriteria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "sub_criteria";
    protected $fillable = [
        'criteria_id',
        'scale',
        'upper_value',
        'under_value',
        'initial_value',
        'final_value',
        'sameas_value'
    ];

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id', 'id');
    }
}
