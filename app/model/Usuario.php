<?php

class Usuario extends Modelo {

    const TABELA = 'usuario';

    public function __construct() {
        $this->banco = Banco::Instanciar();
    }

    final public function inserirUsuario($dados) {
        try {
            $usuario[nome] = $dados['nome'];
            $usuario[sobrenome] = $dados['sobrenome'];
            $usuario[salario] = $dados['salario'];
            $usuario[id_orgao_pagador] = $dados['id_orgao_pagador'];
            $usuario[id_status_usuario] = $dados['id_status_usuario'];

            $salvar_usuario = $this->banco->inserir(static::TABELA, $usuario);
            if ($salvar_usuario == true) {
                $dados_aut[email] = $dados['email'];
                $dados_aut[senha] = $dados['senha'];
                $dados_aut[id_usuario] = $this->pegaUltimoUsuarioInserido();
                $salvar_aut = $this->banco->inserir('autenticacao_user', $dados_aut);
                //var_dump($salvar_aut);die();
                if (!$salvar_aut) {
                    throw new Exception("Houve um erro ao salvar autenticação do usuário!");
                }
            } else {
                throw new Exception("Houve uma falha ao salvar usuário!");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function pegaUltimoUsuarioInserido() {
        return $this->banco->pegaUltimoUsuarioInserido();
    }

    public function pegaSalario($id) {
        return $this->banco->pegaSalarioPorUser(static::TABELA, $id);
    }

    final function autenticacaoUser($email, $senha) {

        return $this->banco->autenticaoUser('autenticacao_user', $email, $senha);
    }

    final function excluirUserAutenticacao($tabela, $id_user) {
        return $this->banco->excluirUserAutenticacao($tabela, $id_user);
    }

    final function alterarUsuario($id_user, $nome, $salario, $id_orgao_pagador, $id_status_usuario) {
        return $this->banco->alterarUsuario(static::TABELA, $id_user, $nome, $salario, $id_orgao_pagador, $id_status_usuario);
    }

    final function alterarUserAut($email, $senha, $id_user) {
        return $this->banco->alterarUserAut($email, $senha, $id_user);
    }

    public function getUsuario($id) {
        return $this->banco->pesquisar('usuario', "id_usuario=$id");
    }

    final function pegaIdLogado() {           
        return $_SESSION['usuario'];
    }

}
