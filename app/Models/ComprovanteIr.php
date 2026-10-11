<?php

namespace App\Models;

use Database\Factories\ComprovanteIrFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['usuario_id', 'ano_base', 'descricao', 'valor', 'arquivo', 'nome_original'])]
class ComprovanteIr extends Model
{
    /** @use HasFactory<ComprovanteIrFactory> */
    use HasFactory;

    /** Disco privado: os arquivos só saem pelo download autorizado. */
    public const DISCO = 'local';

    protected $table = 'comprovantes_ir';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['valor' => 'decimal:2'];
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
