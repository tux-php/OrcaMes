<?php
class PagamentoExtra{        
    
    private int $usuarioId;
    private int $mesReferenciaId;
    private float $valorExtra;

    public function __construct(int $usuarioId = 0, int $mesReferenciaId = 0, float $valorExtra = 0)
    {
        $this->usuarioId = $usuarioId;
        $this->mesReferenciaId = $mesReferenciaId;
        $this->valorExtra = $valorExtra;
    }
    public function carregarUsuarioId():int
    {
        return $this->usuarioId;
    }
    public function carregarMesReferenciaId():int
    {
        return $this->mesReferenciaId;
    }
    public function carregaValorExtra():float
    {
        return $this->valorExtra;
    }
}