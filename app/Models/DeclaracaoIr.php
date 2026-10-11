<?php

namespace App\Models;

use App\Enums\ResultadoIr;
use Database\Factories\DeclaracaoIrFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['usuario_id', 'ano_base', 'resultado', 'valor', 'observacao'])]
class DeclaracaoIr extends Model
{
    /** @use HasFactory<DeclaracaoIrFactory> */
    use HasFactory;

    protected $table = 'declaracoes_ir';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'resultado' => ResultadoIr::class,
            'valor' => 'decimal:2',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
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
