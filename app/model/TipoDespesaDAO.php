<?php
class TipoDespesaDAO{
    
    final public function inserirTD($id_user,$ch,$desc){ 
        try {
            $query = "INSERT INTO tipo_despesa(id_usuario,chave,descricao)
                            VALUES ('$id_usuario','$ch','$descricao')";
                $conexao = Conexao::pegaConexao();
                $stmt = $conexao->prepare($query);
                $rs = $stmt->execute();
                if ($rs) {
                    return true;
                }
                return false;
        } catch (Exception $e) {
            echo "Falha ao inserir Tipo de Despesa. ".$e->getMessage();
        }
    }
    
    final public function listarTD($id_user){
        try {
            $query = "SELECT * FROM tipo_despesa WHERE id_usuario = $id_user and d_e_l_e_t_e is null";
            $conexao = Conexao::pegaConexao();        
            $rs = $conexao->query($query);
            if($rs){
                return $rs->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            echo "Falha ao carregar Lista tipo de Despesa. ".$e->getMessage();
        }
    }

    final public function listar(){
        try {
            $query = "SELECT * FROM tipo_despesa WHERE d_e_l_e_t_e is null";
            $conexao = Conexao::pegaConexao();        
            $rs = $conexao->query($query);
            if($rs){
                return $rs->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            echo "Falha ao carregar Lista tipo de Despesa. ".$e->getMessage();
        }
    }

    public function buscar($id) {
        try{
            $conexao = Conexao::pegaConexao();
            $results = $conexao->query("SELECT * FROM tipo_despesa WHERE id_tipo_despesa = '$id' and d_e_l_e_t_e is null");            
            return $results->fetch();
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }
        
    }
}
?>