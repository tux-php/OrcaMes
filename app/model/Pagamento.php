<?php

class Pagamento extends Modelo {    

    const TABELA = 'pagamentos';

    public function __construct($id = NULL) {
        $this->id = $id;
        parent::__construct();
    }

    final public function listarPagamentoMes($id_mes, $id_usuario) {
        
        return $this->banco->listarPagamentoMes(static::TABELA, $id_mes, $id_usuario);
    }
    
    public function cancelarPag($id) {
        $status = $this->pegaStatus('NPG');
        $cod = $status['id_status_pagamento'];
        $data_proc = date('Y-m-d');
        $salvar = $this->alterarStatusPagamento($cod, $data_proc, $id);
        if ($salvar) {
            return true;
        }
        return false;
    }
    
    public function processarPag($id) {           
        $status = $this->pegaStatus('PG');  
        $cod = $status['id_status_pagamento'];
        $data_proc = date('Y-m-d');        
        if (isset($status)) {
            $salvar = $this->alterarStatusPagamento($cod, $data_proc, $id);
            if ($salvar) {
                return true;
            }
            return false;
        }
    }
    
    public function alterarStatusPagamento($cod, $data_proc, $id) {
        return $this->banco->alterarStatusPagamento(static::TABELA, $cod, $data_proc, $id);
    }
    
     public function validandoMesReferenciaVazio($mes_ref,$id_usuario){
         
        return $this->banco->validarMesRefVazio(static::TABELA,$mes_ref,$id_usuario);
    }

    public function alterarPagamento($id, $id_tab, $dados){
        //var_dump($dados);die();
        return $this->banco->alterarPagamento(static::TABELA,$id,$id_tab,$dados);
    }

}
