<?php
class StatusPagamento extends Modelo{
    const TABELA = 'status_pagamento';
    
    public function __construct() {
        $this->banco = Banco::Instanciar();
    }
}