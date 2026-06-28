<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pasien extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function antreans(): HasMany
    {
        return $this->hasMany(Antrean::class);
    }

    public function rekamMedis(): HasMany
    {
        return $this->hasMany(RekamMedis::class);
    }
}
