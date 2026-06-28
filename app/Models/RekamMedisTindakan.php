<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekamMedisTindakan extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function rekamMedis(): BelongsTo
    {
        return $this->belongsTo(RekamMedis::class);
    }

    public function tindakan(): BelongsTo
    {
        return $this->belongsTo(Tindakan::class);
    }
}
