<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Criteria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "criterias";
    protected $fillable = [
        'name',
        'category',
        'weight'
    ];

    public function sub_criteria()
    {
        return $this->hasMany(SubCriteria::class, 'criteria_id', 'id');
    }
}
