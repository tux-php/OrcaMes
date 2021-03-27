<?php

class OrgaoDAO{       

    public function listarOP()
    {
        try
        {
            $lista = "SELECT * FROM orgao_pagador WHERE d_e_l_e_t_e is null";
            $conexao = Conexao::pegaConexao();        
            $rs = $conexao->query($lista);        
        return $rs->fetchAll(PDO::FETCH_ASSOC);

        }catch(Exception $e){
            echo "Houve um erro na construção da lista .".$e->getMessage();
        }
        
    }
    public function inserirOP(Orgao $orgao)
    {
        try{
            $conexao = Conexao::pegaConexao();
            $query = $conexao->prepare("INSERT INTO orgao_pagador(chave,descricao) VALUES('{$orgao->carregaChave()}','{$orgao->carregaDescricao()}')");        
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
            $conexao = Conexao::pegaConexao();
            $conexao->query("UPDATE orgao_pagador SET $sets WHERE $id_tabela='$id'");
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }   
        
    }

    public function excluir($id):string
    {
        try
        {
            $sql = "DELETE FROM orgao_pagador WHERE id_orgao_pagador = '$id'";        
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
    public function buscar($identificador_id, $id) {
        try{
            $conexao = Conexao::pegaConexao();
            $results = $conexao->query("SELECT * FROM orgao_pagador WHERE $identificador_id = '$id' and d_e_l_e_t_e is null");            
            return $results->fetch();
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }
        
    }
}
?>