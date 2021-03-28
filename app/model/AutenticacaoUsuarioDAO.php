<?php
class AutenticacaoUsuarioDAO
{
    public function inserirUsuarioAutenticacao(AutenticacaoUsuario $autUser, int $usuarioId)
    {
        $query = "INSERT INTO autenticacao_user(email,senha,id_usuario) 
                         VALUES('{$autUser->carregaEmail()}',
                                '{$autUser->carregaSenha()}',
                                 {$usuarioId})";
        $conexao = Conexao::pegaConexao();
        $stmt = $conexao->prepare($query);        
        $rs = $stmt->execute();
        if($rs){
            return TRUE;
            exit();
        }
    }

    final function alterarUserAut($email, $senha, $id_user) {
        try
        {
            $query = "UPDATE autenticacao_user SET email = '{$email}',
                                             senha = '{$senha}'
                                             WHERE id_usuario = $id_user";        
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();
            if ($rs)
                return true;
            return false;
        }catch(Exception $e){
            echo "Falha ao alterar autenticação de segurança. ".$e->getMessage();
        }
        
    }

    public function buscarUsuarioAutenticacao($id) {
        try
        {
            $conexao = Conexao::pegaConexao();
            $query = "SELECT * FROM autenticacao_user WHERE id_usuario = '$id' AND d_e_l_e_t_e IS NULL";            
            $results = $conexao->query($query);            
                if($results){
                    return $results->fetch();
                    exit();
                }
        }catch(Exception $e){
            echo "Houve falha ao encontrar Usuário e sua Autenticação. ".$e->getMessage();
        }
    }

    final function excluirUserAutenticacao($id_user) {
        try
        {   
            $query = "DELETE FROM autenticacao_user WHERE id_usuario = '$id_user'";            
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();
            if($rs){
                return TRUE;
                exit();
            }
        }catch(Exception $e){
            echo "Houve falha ao excluir usuário. ".$e->getMessage();
        }
        
    }

}
?>