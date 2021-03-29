<?php
class TipoDespesa{        
    private int $usuarioId;
    private string $chave;
    private string $descricao;    

    public function __construct(int $usuarioId = 0, string $chave = '',string $descricao = '') {
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