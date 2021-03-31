<?php
class StatusUsuarioDAO{
    public function listar() {
        try
        {
            $lista = "SELECT * FROM status_usuario WHERE d_e_l_e_t_e is null";
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($lista);
            return $rs->fetchAll(PDO::FETCH_ASSOC);                
        }catch(Exception $e){
            echo "Houve uma falha ao gerar lista de usuários. ".$e->getMessage();
        }
        
    }

    public function inserir(StatusUsuario $su)
    {
        try{
            $conexao = Conexao::pegaConexao();
            $query = $conexao->prepare("INSERT INTO status_usuario(chave,descricao) VALUES('{$su->carregarChave()}','{$su->carregarDescricao()}')");        
            $stmt = $query->execute();            
            if(!$stmt)
            {
                throw new Exception("Falha ao inserir os campos na base de dados!");
            }
            return $stmt;            
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();

        }
    }

    public function alterar($id, $id_tabela, $dados) {  
        try
        {
            foreach ($dados as $campo => $valor) {
                $set[] = "$campo='$valor'";
            }
            $sets = strtoupper(implode(',', $set));
            $query = "UPDATE status_usuario SET $sets WHERE $id_tabela='$id'";
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $stmt->execute();            
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }   
        
    }

    public function buscar($identificador_id, $id) {
        try{
            $conexao = Conexao::pegaConexao();
            $results = $conexao->query("SELECT * FROM status_usuario WHERE $identificador_id = '$id' and d_e_l_e_t_e is null");            
            return $results->fetch();
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }
        
    }

    public function excluir($id)
    {
        try
        {
            $sql = "DELETE FROM status_usuario WHERE id_status_usuario = '$id'";        
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($sql);
            if ($rs) {
                return true;
            }
            return false;
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }
        
    }
}