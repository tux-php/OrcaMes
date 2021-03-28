<?php

class TipoPagamento{
    private string $descricao;
    private $tipoDespesa;

    public function __construct(string $descricao = '', $tipoDespesa = '') {
        $this->descricao = $descricao;        
        $this->tipoDespesa = $tipoDespesa;
    }

    public function carregaDescricao():string
    {
        return $this->descricao;
    }
    public function carregaTipoDespesaId()
    {
        return $this->tipoDespesa;
    }
    
    

}
