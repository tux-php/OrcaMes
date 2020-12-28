<?php

class MesReferencia extends Modelo {

    const TABELA = 'mes_referencia';

    public function __construct() {
        parent::__construct();
    }

    public function listarMes() {
        return $this->banco->listarMes(static::TABELA);
    }

    final public function inserirMesRef($ch, $valor) {        

        $this->banco->inserirMesRef(static::TABELA, $ch, $valor);
    }

    public function pegaMesAnterior($id_mes_ref) {
        try {
            if (isset($id_mes_ref)) {
                return $this->banco->pegaMesAnterior(static::TABELA, $id_mes_ref);
            } throw new Exception('Falha ao selecionar mês anterior!');
        } catch (Exception $msg) {
            echo $msg->getMessage() . '<br />';
        }
    }

    public function listarPagamentoMes($id_mes, $id_usuario = null) {        
        return $this->banco->listarPagamentoMes(static::TABELA, $id_mes, $id_usuario);
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

    final function validarMesAno($chave) {

        return $this->banco->buscarMesAno('mes_referencia', $chave);
    }

    final function pegaMesRef() {         
        //var_dump($_REQUEST);
        //var_dump($_SESSION);
        $mes_ref = $_REQUEST['id_mes_referencia'];
        if(isset($mes_ref)){
            return $mes_ref;
        }
    }
}
