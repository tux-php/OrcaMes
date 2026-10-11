<?php

namespace App\Models;

use Database\Factories\CategoriaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nome'])]
class Categoria extends Model
{
    /** @use HasFactory<CategoriaFactory> */
    use HasFactory;

    public function contas(): HasMany
    {
        return $this->hasMany(Conta::class);
    }
}
