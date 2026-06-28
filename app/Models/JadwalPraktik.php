<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPraktik extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function antreans(): HasMany
    {
        return $this->hasMany(Antrean::class);
    }
}
