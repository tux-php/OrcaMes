<?php

class TipoPagamento extends Modelo {

    const TABELA = 'tipo_pagamento';

    public function __construct() {
        $this->banco = Banco::Instanciar();
    }
    
    final public function listarTP($id_usuario) {        
        return $this->banco->listarTP(static::TABELA,$id_usuario);
    }
    
    final public function recuperarIdTP(){        
        return $this->banco->recuperarIdTP();
    }
    
    final public function inserirTP($desc,$id_tp_desp){
        return $this->banco->inserirTP(static::TABELA,$desc,$id_tp_desp);
    }

}
