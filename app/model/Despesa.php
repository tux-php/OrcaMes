<?php
class Despesa extends Modelo{    
    const TABELA = 'despesa';
    
    public function __construct() {
        $this->banco = Banco::Instanciar();
    }
}