<?php

class Banco {

    private static $instance;
    private $conexao;
    private $driver = "mysql";
    private $host = "localhost";
    private $db = "orcamentos";
    private $user = "fernando";
    private $pass = "123456";

    /*
     * Padrão singleton
     */

    public static function Instanciar() {
        if (!self::$instance) {
            self::$instance = new Banco();
            self::$instance->conectar();
        }
        return self::$instance;
    }

    private function conectar() {
        try {
            $this->conexao = new PDO("{$this->driver}:host={$this->host};dbname={$this->db}", "$this->user", "$this->pass");

            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexao->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
        } catch (PDOException $e) {
            print "Erro: " . $e->getMessage() . "\n";
            die();
        }
    }

    public function listar($tabela) {
        $lista = "SELECT * FROM $tabela WHERE d_e_l_e_t_e is null";
        $rs = $this->conexao->query($lista);
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarMes($tabela) {
        $lista = "SELECT * FROM $tabela WHERE d_e_l_e_t_e is null and id_mes_referencia between 253 and 265";
//Inserir uma páginacao aqui
        $rs = $this->conexao->query($lista);
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTP($tabela,$id_usuario) {
        $lista = "SELECT distinct a.* FROM $tabela a, usuario_tipo_pagamento b
        WHERE(a.d_e_l_e_t_e is null)
        AND a.id_tipo_pagamento = b.id_tipo_pagamento AND b.id_usuario = $id_usuario order by a.descricao";
     
        $rs = $this->conexao->query($lista);
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function listarPorUsuario($tabela,$id_usuario){
        $sql = "SELECT * FROM $tabela WHERE id_usuario = $id_usuario and d_e_l_e_t_e is null";
        $rs = $this->conexao->query($sql);
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recuperarIdTP() {
        $sql = $this->conexao->prepare("select id_tipo_pagamento from tipo_pagamento order by id_tipo_pagamento desc limit 1");

        $sql->execute();
        while ($row = $sql->fetch()) {
            $id_usuario = (int) $row['id_tipo_pagamento'];
            return $id_usuario;
        }
    }

    /*
     * LISTA DETALHADA PELO MES REFERENCIADO
     */

    public function listarPagamentoMes($tabela, $id_mes_ref, $id_usuario) {
        $lista = "SELECT * FROM $tabela WHERE id_mes_referencia = $id_mes_ref ";
        //var_dump($lista);die();

        if (isset($id_usuario)) {
            $lista .= " and id_usuario = $id_usuario ";
        }
        $lista .= " AND d_e_l_e_t_e IS NULL";
        $rs = $this->conexao->query($lista);
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarValorSubtotal($tabela, $mes_referencia, $id_usuario) {
        $rs = $this->conexao->query("select valor_pagamento from $tabela "
                . "where id_usuario = $id_usuario "
                . "and id_mes_referencia = $mes_referencia and d_e_l_e_t_e is null");
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarValorItem($tabela, $mes_referencia, $id_usuario, $id_status_pagamento) {
        $rs = $this->conexao->query("select  valor_pagamento from $tabela where id_usuario = $id_usuario and id_status_pagamento = $id_status_pagamento and id_mes_referencia = $mes_referencia and d_e_l_e_t_e is null");
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pesquisar($tabela, $condicao) {
        $rs = $this->conexao->query("SELECT * FROM $tabela WHERE $condicao");
        if (!$rs) {
            throw new Exception(uft8_decode("Houve erro ao executar pesquisa: ") . mysql_error());
        }
        return mysql_fetch_assoc($rs);
    }

    public function query($query) {
        //var_dump($query);die();
//var_dump($this->conexao->exec($query));die();
        $result = $this->conexao->exec($query);
        if (!$result) {
            throw new Exception(utf8_decode("Houve um erro ao excluir arquivo: ") . mysql_error());
        }

        $output = array();
        if (is_resource($result)) {

            while ($row = mysql_fetch_assoc($result)) {
                $output[] = $row;
            }
            return $output;
        }
    }

    public function inserirMesRef($tabela, $chave, $valor) {
        $sql = $this->conexao->prepare("INSERT INTO $tabela (chave,descricao) VALUES ('$chave','$valor')");
        $stmt = $sql->execute();
        if ($stmt) {
            return true;
        }
        return false;
    }

    final public function inserirTP($tabela, $desc, $id_tipo_despesa) {
        $sql = $this->conexao->prepare("INSERT INTO $tabela (descricao,id_tipo_despesa) VALUES ('$desc','$id_tipo_despesa')");
        $stmt = $sql->execute();
        if ($stmt) {
            return true;
        }
        return false;
    }

    final public function inserirUTP($tabela, $id_user, $id_tp) {
        $sql = $this->conexao->prepare("INSERT INTO $tabela (id_usuario,id_tipo_pagamento) VALUES ('$id_user','$id_tp')");
        $stmt = $sql->execute();
        if ($stmt) {
            return true;
        }
        return false;
    }

    final public function inserirTD($tabela, $id_usuario, $ch, $descricao) {
        $sql = $this->conexao->prepare("INSERT INTO $tabela (id_usuario,chave,descricao)"
                . " VALUES ('$id_usuario','$ch','$descricao')");
        //var_dump($sql);die();
        $stmt = $sql->execute();
        if ($stmt) {
            return true;
        }
        return false;
    }

    final public function inserirPagExtra($tabela, $id_user, $id_mes_ref, $valor_extra) {
        $sql = $this->conexao->prepare("INSERT INTO $tabela (id_usuario,id_mes_referencia,valor_extra) VALUES ('$id_user','$id_mes_ref','$valor_extra')");
        $stmt = $sql->execute();
        if ($stmt) {
            return true;
        }
        return false;
    }

    public function inserir($tabela, $dados) {

        try {
            foreach ($dados as $campo => $valor) {
                $campos[] = $campo;
                $valores[] = "$valor";
                $holders[] = '?';
            }
            $campos = implode(',', $campos);
            $holders = implode(',', $holders);
            $st = $this->conexao->prepare("INSERT INTO $tabela ($campos) VALUES ($holders)");
            $rs = $st->execute($valores);

            if (!$rs) {
                throw new Exception("Falha ao inserir os campos " . $holders . " na base de dados!");
            }
            return $rs;
        } catch (Exception $exc) {
            echo $exc->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }
    }

    public function pegaUltimoUsuarioInserido() {
        $sql = $this->conexao->prepare("select id_usuario from usuario order by id_usuario desc limit 1");
        $sql->execute();
        while ($row = $sql->fetch()) {
            $id_usuario = (int) $row['id_usuario'];
            return $id_usuario;
        }
    }

    public function alterar($tabela, $id_tabela, $id, $dados) {
        foreach ($dados as $campo => $valor) {
            $set[] = "$campo='$valor'";
        }
        $sets = strtoupper(implode(',', $set));
        $this->query("UPDATE $tabela SET $sets WHERE $id_tabela='$id'");
    }

    public function alterarUsuario($tabela, $id_user, $nome, $salario, $id_orgao_pagador, $id_status_usuario) {
        $sql = "UPDATE $tabela set nome = '$nome', salario = '$salario', id_orgao_pagador = $id_orgao_pagador,"
                . "id_status_usuario = $id_status_usuario where id_usuario = $id_user";
        $rs = $this->conexao->query($sql);
        if ($rs)
            return true;
        return false;
    }

    public function alterarUserAut($email, $senha, $id_user) {
        $sql = "UPDATE autenticacao_user SET email = '$email', senha = '$senha' where id_usuario = $id_user";
        //var_dump($sql);die();
        $rs = $this->conexao->query($sql);
        if ($rs)
            return true;
        return false;
    }

    public function buscarSalarioExtra($tabela, $id_mes_referencia, $id_usuario) {
        $sql = $this->conexao->prepare("SELECT valor_extra FROM $tabela"
                . " WHERE id_mes_referencia = $id_mes_referencia AND id_usuario = $id_usuario"
                . " ORDER BY id_pag_extra DESC limit 1");
        $stmt = $sql->execute();
        if ($stmt) {
            while ($linha = $sql->fetch()) {
                return $linha['valor_extra'];
            }
        }
    }

    public function excluir($tabela, $id_tabela, $id) {
        $sql = "UPDATE $tabela SET d_e_l_e_t_e = 'S' WHERE $id_tabela = '$id'";

        $rs = $this->conexao->query($sql);
        if ($rs) {
            return true;
        }
        return false;
    }

    public function excluirUserAutenticacao($tabela, $id_user) {
        $this->query(("UPDATE $tabela SET d_e_l_e_t_e = 'S' WHERE id_usuario = '$id_user'"));
    }

    public function buscar($tabela, $identificador_id, $id) {  
        
        $results = $this->conexao->query("SELECT * FROM $tabela WHERE $identificador_id = '$id'");
        //var_dump($results);die();

        return $results->fetch();
    }

    public function buscarPagamento($tabela, $id_usuario, $id_mes_referencia) {
        $results = $this->conexao->query("select * from $tabela where id_usuario = $id_usuario and id_mes_referencia = $id_mes_referencia");

        return $results->fetch();
    }

    public function pegaSalarioPorUser($tabela, $id) {
        //var_dump("SELECT salario FROM $tabela where id_usuario = '$id'");die();
        $rs = $this->conexao->query("SELECT salario FROM $tabela where id_usuario = '$id'");
        //var_dump($rs);die();
        return $rs->fetch();
    }

    public function buscarMesAno($tabela, $chave) {
        $rs = $this->conexao->query("SELECT * FROM $tabela WHERE chave = '$chave' and d_e_l_e_t_e is null");
        if ($rs->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function pegaStatusPago($tabela) {
        $chave = 'PG';
        $rs = $this->conexao->query("SELECT * FROM $tabela where chave = '{$chave}'");
        if ($rs) {
            return $rs->fetch();
        }
    }

    public function pegaStatus($tabela, $chave) {
        $rs = $this->conexao->query("SELECT id_status_pagamento FROM $tabela WHERE chave = '{$chave}'");
        if ($rs) {
            return $rs->fetch();
        }
    }

    public function pegaValorUnidade($tabela, $id) {
        $rs = $this->conexao->query("SELECT valor_pagamento FROM $tabela WHERE id_pagamento = $id");
        return $rs->fetch();
    }

    public function alterarStatusPagamento($tabela, $cod, $data_proc, $id) {
        $rs = $this->conexao->query("update $tabela set id_status_pagamento = $cod, data_processamento = '$data_proc' where id_pagamento = $id ");
        //var_dump($rs);die();
        if ($rs) {
            return 'Pagamento Processado com sucesso!';
        } else {
            return 'Falha ao processar pagamento!';
        }
    }
    
    /**
     * 
     * @param type $tabela
     * @param type $email
     * @param type $senha
     * @return boolean
     * @throws Exception
     * Funcao responsável pela autenticação de Login
     */
    final function autenticaoUser($tabela, $email, $senha) {
        //var_dump("select id_usuario FROM $tabela where email = '{$email}' and senha = '{$senha}'");die();
        try {
            $sql = $this->conexao->prepare("select id_usuario FROM $tabela where email = '{$email}' and senha = '{$senha}'");
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

    public function pegaLogado($tabela, $email, $senha) {
        $sql = $this->conexao->prepare("select id_usuario FROM $tabela where email = '$email' and senha = '{$senha}' and d_e_l_e_t_e is null");
        $sql->execute();
        $rs = $sql->fetch();
        if ($rs) {
            return $rs['id_usuario'];
        }
        return false;
    }

    public function pegaMesAnterior($tabela, $id_mes_referencia) {
        try {
            $sql = $this->conexao->prepare("select id_mes_referencia FROM $tabela 
            WHERE id_mes_referencia = (select max(id_mes_referencia) from $tabela 
            WHERE id_mes_referencia < $id_mes_referencia)");
            //var_dump($sql);die();
            $sql->execute();
            $rs = $sql->fetch();
            $num = $sql->rowcount();
            if ($num > 0) {
                return $rs['id_mes_referencia'];
            } else {
                throw new Exception("Não existe mês precedente!");
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function clonarPagamento($tabela, array $dados) {
        /*
         * tratar falha conexao com banco
         * fazer um verificador para não permitir colocar dados clonados outra vez
         * 
         */
        try {
            $sql = $this->conexao->prepare("insert into $tabela
         (id_tipo_pagamento,valor_pagamento,id_usuario,id_status_pagamento) 
            select a.id_tipo_pagamento,a.valor_pagamento,a.id_usuario,a.id_status_pagamento from pagamentos a, tipo_pagamento b, 
            mes_referencia c, usuario d where 
            (a.d_e_l_e_t_e is null and b.d_e_l_e_t_e is null and c.d_e_l_e_t_e is null and 
            d.d_e_l_e_t_e is null)
             and a.id_tipo_pagamento = b.id_tipo_pagamento
             and a.id_mes_referencia = c.id_mes_referencia and a.id_usuario = d.id_usuario 
             and a.id_mes_referencia = $dados[id_mes_clone] and a.id_usuario = $dados[id_usuario]");

            $sql->execute();
            $num = $sql->rowcount();
            if ($num > 0) {
                $this->processarClonagem($tabela, $dados);
                return true;
            } else {
                throw new Exception("Mês precedente encontra-se vazio!");
            }
        } catch (Exception $exc) {
            echo $exc->getMessage() . '<br>';
        }
    }

    private function processarClonagem($tabela, $dados) {
        $sql = $this->conexao->prepare("update $tabela set "
                . "data_lancamento = '$dados[data_lancamento]', 
            id_status_pagamento = $dados[id_status_pagamento], "
                . "id_mes_referencia = $dados[id_mes_referencia],
                ch_clone = '$dados[ch_clone]'
            where data_lancamento = '0000-00-00' and id_usuario = $dados[id_usuario] "
                . "and d_e_l_e_t_e is null;");
        $rs = $sql->execute();
        if ($rs) {
            return true;
        }
        return false;
    }

    public function gerarRelatorio() {

        $rs = $this->conexao->query("select distinct(a.descricao), sum(b.valor_pagamento)
                        from mes_referencia a, pagamentos b
                        where(a.d_e_l_e_t_e is null and b.d_e_l_e_t_e is null)
                        and a.id_mes_referencia = b.id_mes_referencia
                        and b.data_processamento between '2017-01-01' and '2017-12-30'
                        group by a.descricao order by a.id_mes_referencia");
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPagamentoIR($tabela, $id_user) {
        $sql = "SELECT ir.id_ir,ir.descricao,ir.valor,ir.imagem,ir.data_processamento FROM $tabela as ir
            INNER JOIN usuario as users
            INNER JOIN tipo_despesa as tp
            ON(ir.d_e_l_e_t_e is null AND users.d_e_l_e_t_e is null AND tp.d_e_l_e_t_e is null)
            AND ir.id_usuario = users.id_usuario
            AND ir.id_tipo_despesa = tp.id_tipo_despesa
            AND ir.id_usuario = $id_user";
        $rs = $this->conexao->query($sql);
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

}
