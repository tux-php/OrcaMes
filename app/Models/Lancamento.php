<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\LancamentoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['conta_id', 'competencia', 'valor', 'lancado_em', 'pago_em', 'observacao'])]
class Lancamento extends Model
{
    /** @use HasFactory<LancamentoFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'competencia' => 'immutable_date',
            'lancado_em' => 'immutable_date',
            'pago_em' => 'immutable_date',
            'valor' => 'decimal:2',
        ];
    }

    public function conta(): BelongsTo
    {
        return $this->belongsTo(Conta::class);
    }

    public function donoId(): int
    {
        return $this->conta->usuario_id;
    }

    public function estaPago(): bool
    {
        return $this->pago_em !== null;
    }

    /**
     * @param  array<int, int>  $ids
     */
    public function scopeDosUsuarios(Builder $query, array $ids): void
    {
        $query->whereIn('conta_id', Conta::query()->select('id')->whereIn('usuario_id', $ids));
    }

    public function scopeDaCompetencia(Builder $query, CarbonInterface $competencia): void
    {
        $query->whereDate('competencia', $competencia->toDateString());
    }
}
