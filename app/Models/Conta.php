<?php

namespace App\Models;

use Database\Factories\ContaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['usuario_id', 'categoria_id', 'nome', 'recorrente', 'dedutivel_ir', 'ativa'])]
class Conta extends Model
{
    /** @use HasFactory<ContaFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recorrente' => 'boolean',
            'dedutivel_ir' => 'boolean',
            'ativa' => 'boolean',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function lancamentos(): HasMany
    {
        return $this->hasMany(Lancamento::class);
    }

    public function donoId(): int
    {
        return $this->usuario_id;
    }

    /**
     * @param  array<int, int>  $ids
     */
    public function scopeDosUsuarios(Builder $query, array $ids): void
    {
        $query->whereIn('usuario_id', $ids);
    }
}
