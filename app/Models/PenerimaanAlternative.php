<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenerimaanAlternative extends Model
{
    use SoftDeletes;
    protected $table = 'alternative_penerimaans';
    protected $fillable = [
        'penerimaan_id',
        'alternative_id',
        'rank',
        'value',
    ];

    public function alternative()
    {
        return $this->belongsTo(Alternative::class, 'alternative_id', 'id');
    }

    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'penerimaan_id', 'id');
    }
}
