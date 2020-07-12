<?php
class PagamentoExtra extends Modelo{    
    const TABELA = 'pagamento_extra';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function inserirPagExtra($id_user,$mes_ref,$valor){
        //var_dump($user,$tp);die();
        return $this->banco->inserirPagExtra(static::TABELA,$id_user,$mes_ref,$valor);
    }
    
    final function pegaSalarioExtra($id_mes_ref,$id_usuario) {
        return $this->banco->buscarSalarioExtra(static::TABELA, $id_mes_ref,$id_usuario);
    }
}