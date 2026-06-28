<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rujukan extends Model
{
    protected $guarded = ['id'];
    const UPDATED_AT = null; // Sesuai dengan DBML yang hanya mensyaratkan created_at

    public function rekamMedis(): BelongsTo
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
