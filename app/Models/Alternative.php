<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alternative extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "alternatives";
    protected $fillable = [
        'name',
        'description',
    ];

    public function criteria_alternative()
    {
        return $this->hasMany(CriteriaAlternative::class, 'alternative_id', 'id');
    }
}
