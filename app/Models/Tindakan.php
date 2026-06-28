<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tindakan extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function rekamMedisTindakans(): HasMany
    {
        return $this->hasMany(RekamMedisTindakan::class);
    }
}
