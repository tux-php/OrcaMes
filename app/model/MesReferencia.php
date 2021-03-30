<?php

class MesReferencia{    

    public function listarMes() {
        try {
            $query = "SELECT * FROM mes_referencia WHERE d_e_l_e_t_e IS NULL
                           AND id_mes_referencia BETWEEN 265 and 277";        
            //Inserir uma paginação aqui
                    $conexao = Conexao::pegaConexao();
                    $rs = $conexao->query($query);
                    if($rs){
                        return $rs->fetchAll(PDO::FETCH_ASSOC);
                        exit;
            }
        }catch(Exception $e){
            echo "Falha ao carregar listagem mês referência .".$e->getMessage();
        }
    }

    final public function inserirMesRef($ch, $valor) {   
        try {
            $query = "INSERT INTO mes_referencia(chave,descricao) VALUES ('$chave','$valor')";
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $rs = $stmt->execute();
            if ($rs) {
                return true;
                exit;
            }
        } catch (Exception $e) {
            echo "Falha ao inserir Mês Referência. ".$e->getMessage();
        }     

        
    }

    public function pegaMesAnterior($id_mes_ref) {
        try {
            if (isset($id_mes_ref)) {
                $query = "SELECT id_mes_referencia FROM mes_referencia 
                                WHERE id_mes_referencia = (SELECT max(id_mes_referencia) from mes_referencia 
                                WHERE id_mes_referencia < $id_mes_ref)";
            //var_dump($sql);die();
            $conexao = Conexao::pegaConexao();
            $stmt = $conexao->prepare($query);
            $stmt->execute();
            $rs = $stmt->fetch();
            $num = $stmt->rowcount();
            if ($num > 0) {
                return $rs['id_mes_referencia'];
            } else {
                throw new Exception("Não existe mês precedente!");
            }
            } throw new Exception('Falha ao selecionar mês anterior!');
        } catch (Exception $e) {
            echo "Falha ao recuperar mês precedente. ".$e->getMessage() . '<br />';
        }
    }

    public function listarPagamentoMes($id_mes, $id_usuario = null) {        
        $lista = "SELECT * FROM $tabela WHERE id_mes_referencia = $id_mes_ref ";
        //var_dump($lista);die();

        if (isset($id_usuario)) {
            $lista .= " and id_usuario = $id_usuario ";
        }
        $lista .= " AND d_e_l_e_t_e IS NULL";
        //var_dump($lista);die();
        $rs = $this->conexao->query($lista);
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    //Gerador de Meses automático
    final function geradorMA() {
        $meses_anos = ["JAN" => "JANEIRO",
            "FEV" => "FEVEREIRO",
            "MAR" => "MARÇO",
            "ABR" => "ABRIL",
            "MAI" => "MAIO",
            "JUN" => "JUNHO",
            "JUL" => "JULHO",
            "AGO" => "AGOSTO",
            "SET" => "SETEMBRO",
            "OUT" => "OUTUBRO",
            "NOV" => "NOVEMBRO",
            "DEZ" => "DEZEMBRO"
        ];
        return $meses_anos;
    }

    public function pegaMesAutomatico() {        
        $ano_atual = date('Y');
        //$ano_atual = 2021;
        $gerador_ma = $this->geradorMA();
        if ($gerador_ma) {
            foreach ($gerador_ma as $chave => $valor) {
                $ch = $chave . '/' . $ano_atual;
                $val = $valor . '/' . $ano_atual;
                $validaMes = $this->validarMesAno($ch);
                //var_dump($validaMes);die();
                if ($validaMes == false) {
                    $this->inserirMesRef($ch, $val);
                }
            }
        } else {
            throw new Exception("Houve falha ao gerar Mês/Ano automaticamente.");
        }
    }

    public function buscar($id) {
        try{            
            $query = "SELECT * FROM mes_referencia WHERE id_mes_referencia = '$id' AND d_e_l_e_t_e IS NULL";
            //var_dump($query);die();
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->query($query);
            if($rs){
                return $rs->fetch();
                exit;
            }            
        }catch(Exception $e){
            echo $e->getMessage() . " - Detalhe:" . $exc->getTraceAsString();
        }
        
    }

    final function validarMesAno($chave) {

        $rs = $this->conexao->query("SELECT * FROM $tabela WHERE chave = '$chave' and d_e_l_e_t_e is null");
        //var_dump($rs);die();
        if ($rs->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function pegaMesRef() {                
        try{
            if(!isset($_REQUEST['id_mes_referencia'])){                
                $mes_ref = $_SESSION['id_mes_ref'];                
            }
            $mes_ref = @$_REQUEST['id_mes_referencia'];            
            return $mes_ref;
            exit;            
        }catch(Exception $e){
            echo "Falha ao recuperar mês de referência. ".$e->getMessage();
        }        
        
    }
}
