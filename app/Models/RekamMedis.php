<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RekamMedis extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'alergi_obat' => 'boolean',
            'gangguan_ginjal' => 'boolean',
            'menyusui' => 'boolean',
            'butuh_rujukan' => 'boolean',
        ];
    }

    public function antrean(): BelongsTo
    {
        return $this->belongsTo(Antrean::class);
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function resepObats(): HasMany
    {
        return $this->hasMany(ResepObat::class);
    }

    public function rekamMedisTindakans(): HasMany
    {
        return $this->hasMany(RekamMedisTindakan::class);
    }

    public function rujukan(): HasOne
    {
        return $this->hasOne(Rujukan::class);
    }
}
