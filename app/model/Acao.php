<?php

/* @author fernando */

abstract class Acao {

    protected function pegaAction($action, Controle $metodo) {
        $action = isset($_GET['action']) ? $_GET['action'] : 'login';
        switch ($action) {
            case 'inserirOP': $metodo->inserirOP();
                break;
            case 'editarOP': $metodo->editarOP($_GET['id_orgao_pagador']);
                break;
            case 'excluirOP': $metodo->excluirOP($_GET['id_orgao_pagador']);
                break;
            case 'inserirTipoPagamento': $metodo->inserirTipoPagamento();
                break;
            case 'inserirStatuPagamento': $metodo->inserirSP();
                break;
            case 'alterarTP': $metodo->alterarTP($_GET['id_tipo_pagamento']);
                break;
            case 'alterarSP': $metodo->alterarSP($_GET['id_status_pagamento']);
                break;
            case 'excluirTP': $metodo->excluirTP($_GET['id_tipo_pagamento']);
                break;
            case 'excluirSP': $metodo->excluirSP($_GET['id_status_pagamento']);
                break;
            case 'listaTipoPagamento': $metodo->listarTP();
                break;
            case 'listaStatusPagamento': $metodo->listarSP();
                break;
            case 'listarTipoDespesa': $metodo->listarTD();
                break;
            case 'inserirTipoSaldo': $metodo->inserirTipoSaldo();
                break;
            case 'alterarTipoSaldo': $metodo->alterarTipoSaldo($_GET['id_tipo_saldo']);
                break;
            case 'excluirTipoSaldo': $metodo->excluirTipoSaldo($_GET['id_tipo_saldo']);
                break;
            case 'listaOrgaoPagador': $metodo->listaOrgaoPagador();
                break;
            case 'inserirUsuario': $metodo->inserirUsuario();
                break;
            case 'listaUsuario': $metodo->listarUsuario();
                break;
            case 'excluirUsuario': $metodo->excluirUsuario($_GET['id_usuario']);
                break;
            case 'alterarUsuario': $metodo->alterarUsuario($_GET['id_usuario']);
                break;
            case 'inserirPagamento': $metodo->inserirPagamento();

                break;
            case 'alterarPagamento': $metodo->alterarPagamento($_GET['id_pagamento']);
                break;
            case 'excluirPagamento': $metodo->excluirPagamento($_GET['id_pagamento']);
                break;
            case 'detalharPagamentoMes': $metodo->listarPagamento();
                break;
            case 'listarPagamentoMes': $metodo->listarPagamentoMes();
                break;
            case 'processarPagamento': $metodo->processarPagamento($_GET['id_pagamento']);
                break;
            case 'cancelarPagamento': $metodo->cancelarPagamento($_GET['id_pagamento']);
                break;
            case 'clonarPagamentoMes': $metodo->clonarPagamentoMes();
                break;
            case 'adicionarSalarioExtra': $metodo->adicionarSalarioExtra($_GET['id_mes_ref']);
                break;
            case 'listarStatusUsuario': $metodo->listarStatusUsuario();
                break;
            case 'inserirStatusUsuario': $metodo->inserirSU();
                break;
            case 'alterarStatusUsuario': $metodo->alterarSU($_GET['id_status_usuario']);
                break;
            case 'excluirStatusUsuario': $metodo->excluirSU($_GET['id_status_usuario']);
                break;
            case 'inserirMesRef': $metodo->inserirMesRef();
                break;
            case 'listarMesRef': $metodo->listarMesRef();
                break;
            case 'alterarMesRef': $metodo->alterarMesRef($_GET['id_mes_referencia']);
                break;
            case 'excluirMesRef': $metodo->excluirMesRef($_GET['id_mes_referencia']);
                break;
            case 'inserirTD': $metodo->inserirTD();
                break;
            case 'alterarTD': $metodo->alterarTD($_GET['id_tipo_despesa']);
                break;
            case 'excluirTD': $metodo->excluirTD($_GET['id_tipo_despesa']);
                break;
            case 'inserirDespesa': $metodo->inserirDespesa();
                break;
            case 'alterarDespesa': $metodo->alterarDespesa($_GET['id_despesa']);
                break;
            case 'excluirDespesa': $metodo->excluirDespesa($_GET['id_despesa']);
                break;
            case 'listarRelatorios': $metodo->listarRelatorio();
                break;
            case 'home': $metodo->home();
                break;
            case 'inserirPagamentoIR': $metodo->inserirPagamentoIR();
                break;
            case 'alterarPagamentoIR': $metodo->alterarPagamentoIR($_GET['id_pagamento_ir']);
                break;
            case 'excluirPagamentoIR': $metodo->excluirPagamentoIR($_GET['id_pagamento_ir']);
                break;
            case 'listarPagamentosIR': $metodo->listarPagamentoIR();
                break;
            case 'login':
                $usuario = null;
                $senha = null;
                if (!empty($_REQUEST)) {
                    $usuario = $_REQUEST['usuario'];
                    $senha = $_REQUEST['senha'];
                }
                    $metodo->login($usuario, $senha);
                break;
        }
    }

}
