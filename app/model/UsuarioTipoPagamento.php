<?php
class UsuarioTipoPagamento extends Modelo{    
    const TABELA = 'usuario_tipo_pagamento';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function inserirUTP($user,$tp){        
        $this->banco->inserirUTP(static::TABELA,$user,$tp);
    }
}