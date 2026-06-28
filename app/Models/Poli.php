<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poli extends Model
{

    protected $guarded  = ['id'];
    public $timestamps = false; // Nonaktifkan karena tabel master ini tidak butuh created_at

    public function dokters(): HasMany
    {
        return $this->hasMany(Dokter::class);
    }

    public function tindakans(): HasMany
    {
        return $this->hasMany(Tindakan::class);
    }
}
