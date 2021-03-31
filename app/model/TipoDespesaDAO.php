<?php
class TipoDespesaDAO{
    
    final public function inserirTD(tipoDespesa $td){ 
        try {
            $query = "INSERT INTO tipo_despesa(id_usuario,chave,descricao)
                            VALUES ('{$td->carregaUsuarioId()}',
                                    '{$td->carregaChave()}',
                                    '{$td->carregaDescricao()}')";                                    
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
    
    final public function listarTD($id_user = FALSE){                
        try {
            $query = "SELECT * FROM tipo_despesa ";
            if($id_user != FALSE){
                $query .=  " WHERE id_usuario = $id_user ";
            }
            $query .= "ORDER BY descricao";
            $conexao = Conexao::pegaConexao();        
            $rs = $conexao->query($query);
            if($rs){
                return $rs->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            echo "Falha ao carregar Lista tipo de Despesa. ".$e->getMessage();
        }
    }

    final public function alterar($id,$ch,$desc)
    {
        try{
            $query = "UPDATE tipo_despesa SET chave = '{$ch}', descricao = '{$desc}' WHERE id_tipo_despesa = $id";           
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();
            if($rs){
                return TRUE;
                exit;
            }

        }catch(Exception $e){
            echo "Falha ao alterar tipo de despesa $desc .".$e->getMessage();
        }
        
    }

    final public function excluir($id)
    {
        try{
            $query = "DELETE from tipo_despesa WHERE id_tipo_despesa = $id";
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();
            if($rs){
                return TRUE;
                exit;
            }

        }catch(Exception $e){
            echo "Falha ao excluir Tipo de Despesa. ".$e->getMessage();
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