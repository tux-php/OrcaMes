<?php
class TipoDespesa{        
    private string $chave;
    private string $descricao;
    private $usuarioId;

    public function __construct(string $chave = '',string $descricao = '',int $usuarioId = 0) {
        $this->chave = $chave;
        $this->descricao = $descricao;
        $this->usuarioId = $usuarioId;
    }

    public function carregaChave():string
    {
        return $this->chave;
    }
    public function carregaDescricao():string
    {
        return $this->descricao;
    }
    public function carregaUsuarioId():int
    {
        return $this->usuarioId;
    }
    
}