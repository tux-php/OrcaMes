<?php
class TipoDespesa extends Modelo{    
    const TABELA = 'tipo_despesa';
    
    public function __construct() {
        $this->banco = Banco::Instanciar();
    }
    
    final public function inserirTD($id_user,$ch,$desc){
        return $this->banco->inserirTD(static::TABELA,$id_user,$ch,$desc);
    }
    
    final public function listarTD($id_user){
        return $this->banco->listarPorUsuario(static::TABELA,$id_user);
    }
}