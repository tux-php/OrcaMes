<?php
class Orgao extends Modelo{        
    const TABELA = 'orgao_pagador';
    
    public function __construct($id = null) {                
        $this->id = $id;
        $this->banco = Banco::Instanciar();
    }
    
}