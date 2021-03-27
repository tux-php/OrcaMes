<?php
class Orgao{        
    private int $id;
    private string $chave;
    private string $descricao;
    private OrgaoDAO $orgaoDao;
    
    public function __construct(string $chave='', string $descriao='') {        
        $this->chave = $chave;
        $this->descricao = $descriao;        
    }
    public function carregaId():int
    {
        return $this->id;
    }
    
    public function carregaChave():string
    {
        return $this->chave;
    }
    public function carregaDescricao():string
    {
        return $this->descricao;
    }

     
    
}