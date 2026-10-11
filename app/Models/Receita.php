<?php

namespace App\Models;

use App\Enums\TipoReceita;
use Carbon\CarbonInterface;
use Database\Factories\ReceitaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['usuario_id', 'competencia', 'tipo', 'descricao', 'valor'])]
class Receita extends Model
{
    /** @use HasFactory<ReceitaFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'competencia' => 'immutable_date',
            'tipo' => TipoReceita::class,
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

    public function scopeDaCompetencia(Builder $query, CarbonInterface $competencia): void
    {
        $query->whereDate('competencia', $competencia->toDateString());
    }
}
