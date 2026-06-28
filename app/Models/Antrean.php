<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Antrean extends Model
{
    protected $guarded = ['id'];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function jadwalPraktik(): BelongsTo
    {
        return $this->belongsTo(JadwalPraktik::class);
    }

    public function rekamMedis(): HasOne
    {
        return $this->hasOne(RekamMedis::class);
    }
}
