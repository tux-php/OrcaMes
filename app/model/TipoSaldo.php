<?php
class TipoSaldo extends Modelo{
    const TABELA = 'tipo_saldo';
    
    public function __construct() {
        $this->banco = Banco::Instanciar();
    }
}