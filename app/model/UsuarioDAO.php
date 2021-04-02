<?php
class UsuarioDAO
{
    final public function inserirUsuario(Usuario $usuario, AutenticacaoUsuario $userAutenticacao) {        
        try {            
            $query = "INSERT INTO usuario(nome,sobrenome,salario,id_orgao_pagador,id_status_usuario)
                             VALUES('{$usuario->carregaNome()}',
                                    '{$usuario->carregaSobrenome()}',
                                    '{$usuario->carregaSalario()}',
                                     {$usuario->carregaOrgaoId()},
                                     {$usuario->carregaStatusId()})";            
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $salvar_usuario = $stmt->execute();
            if ($salvar_usuario) {  
                $autenticacaoUsuarioDAO = new AutenticacaoUsuarioDAO();
                $idUltimoUsuario = $this->pegaUltimoUsuarioInserido();
                $salvaUsuarioAutenticado = $autenticacaoUsuarioDAO->inserirUsuarioAutenticacao($userAutenticacao,$idUltimoUsuario);                
                if (!$salvaUsuarioAutenticado) {
                    throw new Exception("Houve um erro ao salvar autenticação do usuário!");
                }
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo "Não foi possível salvar Usuário. Informe o Admnistrador do Sistema. ".$e->getMessage();
        }
    }

    public function listar() {
        try
        {
            $lista = "SELECT * FROM usuario WHERE d_e_l_e_t_e is null";
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($lista);
            return $rs->fetchAll(PDO::FETCH_ASSOC);                
        }catch(Exception $e){
            echo "Houve uma falha ao gerar lista de usuários. ".$e->getMessage();
        }
        
    }
    
    public function buscar($id) {
        try{
            $conexao = Conexao::pegaConexao();
            $query = "SELECT * FROM usuario WHERE id_usuario = '$id' AND d_e_l_e_t_e IS NULL";
            $results = $conexao->query($query);
            if($results){
                return $results->fetch();
                exit();
            }
        }catch(Exception $e){
            echo "Houve falha ao encontrar usuário. ".$e->getMessage();
        }
        
        
    }
    public function pegaUltimoUsuarioInserido() {
        try
        {
            $conexao = Conexao::pegaConexao();
            $sql = $conexao->prepare("select id_usuario from usuario order by id_usuario desc limit 1");
            $sql->execute();
            while ($row = $sql->fetch()) {
                $id_usuario = (int) $row['id_usuario'];
            return $id_usuario;
        }}catch(Exception $e){
            echo "Código do usuário não recuperado. ".$e->getMessage();
        }
        
    }

    public function carregarSalario($id) {      
        try
        {            
            $query = "SELECT salario FROM usuario WHERE id_usuario = '$id'";        
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($query);
            if($rs){
                return $rs->fetch();
                exit;
            }            
        }catch(Exception $e){
            echo "Não foi possível recuperar salário do usuário. ".$e->getMessage();
        }  
        
    }

    final function autenticacaoUser($email, $senha) {
        try {
            $conexao = Conexao::pegaConexao();
            $sql = $conexao->prepare("SELECT
                                        id_usuario
                                      FROM autenticacao_user 
                                      WHERE email = '{$email}'
                                      AND senha = '{$senha}' AND d_e_l_e_t_e IS NULL");            
            $stmt = $sql->execute();            
            if ($stmt == false) {
                throw new Exception('Houve um erro ao executar Autenticação do Usuário.');
            }
            $num = $sql->rowCount();
            if ($num > 0)
                return true;
            return false;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function excluirUsuario(int $id){
        try{
            $query = "DELETE FROM usuario WHERE id_usuario = $id";
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();
            if($rs){
                return TRUE;
                exit();
        }
        }catch(Exception $e){
            echo "Usuário não excluído. ".$e->getMessage();
        }
        
    }

    final function alterarUsuario($id_user, Usuario $usuario) {  
        try{
            $nome = $usuario->carregaNome();
            $sobrenome = $usuario->carregaSobrenome();  
            $salario = $usuario->carregaSalario();      
            $query = "UPDATE usuario SET nome = '$nome',
                                    sobrenome = '$sobrenome',
                                    salario = '$salario',
                                    id_orgao_pagador = {$usuario->carregaOrgaoId()},
                                    id_status_usuario = {$usuario->carregaStatusId()}
                                    WHERE id_usuario = $id_user";           
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();
            if ($rs)
                return true;
            return false;
        }catch(Exception $e){
            echo "Usuário não alterado. Favor informar Admnistrador. ".$e->getMessage();
        } 
        
    }

    final function pegaLogado($email, $senha) {        
        try
        {
            $conexao = Conexao::pegaConexao();
            $sql = $conexao->prepare("SELECT id_usuario
                                    FROM autenticacao_user
                                    WHERE email = '$email' 
                                    AND senha = '{$senha}' 
                                    AND d_e_l_e_t_e IS NULL");
            $sql->execute();
            $rs = $sql->fetch();
            if ($rs) {
                return $rs['id_usuario'];
            }
            return false;
        }catch(Exception $e){
            echo "Falha ao recuperar usuário logado. ".$e->getMessage();
        }
        
    }

    public function pegaSalario($id) {        
        try {
            $query = "SELECT salario FROM usuario WHERE id_usuario = '$id'";
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($query);
            if($rs){
                return $rs->fetch();
                exit;
            }
        } catch (Exception $e) {
            echo "Falha ao retornar salário do usuário .".$e->getMessage();
        }
        
    }


}

?>