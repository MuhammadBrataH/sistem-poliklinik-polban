<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function resep_obats(): HasMany
    {
        return $this->hasMany(ResepObat::class);
    }
}
