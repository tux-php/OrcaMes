<?php
class StatusPagamentoDAO{
    public function pegaStatus($chave) {
        try {            
            $query = "SELECT * FROM status_pagamento where chave = '{$chave}'";            
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($query);
            if ($rs) {
                return $rs->fetch();
         }
        } catch (Exception $e) {
            echo "Falha ao recuperar status do pagamento. ".$e->getMessage();
        }
    }

    public function buscar($id) {
        try{
            $conexao = Conexao::pegaConexao();
            $query = "SELECT * FROM status_pagamento WHERE id_status_pagamento = '$id' AND d_e_l_e_t_e IS NULL";
            $results = $conexao->query($query);
            if($results){
                return $results->fetch();
                exit();
            }
        }catch(Exception $e){
            echo "Houve falha ao encontrar usuário. ".$e->getMessage();
        }
    }
}