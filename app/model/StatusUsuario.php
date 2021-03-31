<?php
class StatusUsuario{
    private string $chave;
    private string $descricao;

    public function __construct(string $chave = '', string $descricao = ''){
        $this->chave = $chave;
        $this->descricao = $descricao;
    }
    public function carregarChave():string
    {
        return $this->chave;
    }
    public function carregarDescricao():string
    {
        return $this->descricao;
    }
    
}