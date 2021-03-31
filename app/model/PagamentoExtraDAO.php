<?php
class PagamentoExtraDAO{

    public function inserirPagExtra(PagamentoExtra $pe){
        try {
            $query = "INSERT INTO pagamento_extra (id_usuario,id_mes_referencia,valor_extra) 
                            VALUES ('{$pe->carregarUsuarioId()}',
                                    '{$pe->carregarMesReferenciaId()}',
                                    '{$pe->carregaValorExtra()}')";
                                    //var_dump($query);die();
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $stmt->execute();                    
            if ($stmt) {
                return true;
            }
            return false;    
        } catch (Exception $e) {
            echo "Falha ao inserir Pagamento Extra. ".$e->getMessage();
        }
        
    }
    
    final function pegaSalarioExtra($id_mes_referencia,$id_usuario) {
        try {
            $query = "SELECT valor_extra FROM pagamento_extra
                            WHERE id_mes_referencia = $id_mes_referencia 
                            AND id_usuario = $id_usuario
                            ORDER BY id_pag_extra DESC limit 1";
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($query);
            if ($rs) {
                while ($linha = $rs->fetch()) {
                    return $linha['valor_extra'];
                }
            }
        } catch (Exception $e) {
            echo "Falha ao recuperar Salário Extra. ".$e->getMessage();
        }
        
    }
}