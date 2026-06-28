<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dokter extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function jadwalPraktiks(): HasMany
    {
        return $this->hasMany(JadwalPraktik::class);
    }

    public function rekamMedis(): HasMany
    {
        return $this->hasMany(RekamMedis::class);
    }
}
