<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penerimaan extends Model
{
    use SoftDeletes;
    protected $table = "penerimaans";
    protected $fillable = [
        'tanggal',
        'status',
        'alternativemath',
    ];

    public function rank()
    {
        return $this->hasMany(PenerimaanAlternative::class, 'penerimaan_id', 'id');
    }
}
