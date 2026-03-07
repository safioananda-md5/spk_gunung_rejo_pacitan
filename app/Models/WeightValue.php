<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeightValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "weight_value";
    protected $fillable = [
        'gap',
        'weight'
    ];
}
