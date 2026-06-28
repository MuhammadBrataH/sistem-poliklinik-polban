<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResepObat extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function rekamMedis(): BelongsTo
    {
        return $this->belongsTo(RekamMedis::class);
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }
}
