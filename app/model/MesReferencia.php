<?php

class MesReferencia{    

    /*@ Listagem -Mês/Ano presente na aba Processar Calendário */
    public function listarMes() {
        try {
            $query = "SELECT * FROM mes_referencia WHERE d_e_l_e_t_e IS NULL
                           AND id_mes_referencia BETWEEN 277 and 289";                              
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
            $query = "INSERT INTO mes_referencia(chave,descricao) VALUES ('$ch','$valor')";
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

    public function listarPagamentoMes($id_mes_ref, $id_usuario = null) {        
        try {
            $query = "SELECT * FROM mes_referencia WHERE id_mes_referencia = $id_mes_ref ";
            if (isset($id_usuario)) {
                $query .= " AND id_usuario = $id_usuario ";
            }
            $query .= " AND d_e_l_e_t_e IS NULL";
            $conexao = Conexao::pegaConexao();            
            $rs = $conexao->query($query);
            if($rs):
                return $rs->fetchAll(PDO::FETCH_ASSOC);
                exit;
            endif;            
        } catch (Exception $e) {
            echo "Falha ao identificar mês referenciado. ".$e->getMessage();
        }
        
    }

    //Gerador de Meses automático
    final function gerarMes() {
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

    //Função responsável pela recuperação do mês automático presente no processamento da formação do calendário anual.
    public function pegarMesAutomatico() {        
        try {
            $ano_atual = date('Y');
            //$ano_atual = 2022;
            $meses = $this->gerarMes();
            if ($meses) {
                foreach ($meses as $chave => $valor):
                    $ch = $chave . '/' . $ano_atual;
                    $val = $valor . '/' . $ano_atual;
                    $validaMes = $this->validarMesAno($ch);                    
                    if ($validaMes == false):
                        $this->inserirMesRef($ch, $val);                        
                    endif;
                endforeach;
            } else {
                throw new Exception("Houve falha ao recuperar Mês automático.");
            }
        } catch (Exception $e) {
            echo "Falha no processamento do Mês automático. ".$e->getMessage();
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
        try {
            $query = "SELECT * FROM mes_referencia WHERE chave = '$chave' AND d_e_l_e_t_e IS NULL";            
            $conexao = Conexao::pegaConexao();
            $rs = $conexao->prepare($query);            
            $rs->execute();            
            if ($rs->rowCount() > 0) {
                return true;                
            }
            return false;
        } catch (Exception $e) {
            echo "Falha no processo de validação do Mês/Ano de referência. ".$e->getMessage();
        }
        
    }
    //Recuperação do Mês de referência por Session ou Request.
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
