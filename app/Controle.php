<?php
class Controle extends Acao {
    private $classeNow;
    private Orgao $orgao;
    private OrgaoDAO $orgaoDAO;
    private Usuario $usuario;
    private UsuarioDAO $usuarioDAO;
    private TipoPagamento $tipoPagamento;
    private TipoPagamentoDAO $tipoPagamentoDAO;
    private $tipoSaldo;
    private $salarioMensal;
    private Pagamento $pagamento;
    private PagamentoDAO $pagamentoDAO;
    private $pagamentoExtra;
    private $pagamentoIR;
    private $view;
    private $statusPagamento;
    private $statusUsuario;
    private $mesReferencia;
    private TipoDespesa $tipoDespesa;
    private TipoDespesaDAO $tipoDespesaDAO;
    private $despesa;
    private $relatorio;
    private $usuarioTipoPagamento;

    function __construct() {
        $this->loadClasses();
        $this->pegaAcao();
    }

    private function pegaAcao() {
        $this->pegaAction('login', $this);
    }

    private function loadClasses() {
        $this->orgao = new Orgao();
        $this->orgaoDAO = new OrgaoDAO();
        $this->usuario = new Usuario();
        $this->usuarioDAO = new UsuarioDAO();
        $this->tipoPagamento = new TipoPagamento();
        $this->tipoPagamentoDAO = new TipoPagamentoDAO();
        $this->classeNow = new CreatorConcrete();
        $this->salarioMensal = new SalarioMensal();
        $this->pagamento = new Pagamento();
        $this->pagamentoDAO = new PagamentoDAO();
        $this->pagamentoExtra = new PagamentoExtra();
        $this->pagamentoIR = new PagamentoIR();
        $this->view = new View();
        $this->statusPagamento = new StatusPagamento();
        $this->statusUsuario = new StatusUsuario();
        $this->mesReferencia = new MesReferencia();
        $this->tipoDespesa = new TipoDespesa();
        $this->tipoDespesaDAO = new TipoDespesaDAO();
        $this->despesa = new Despesa();
        $this->relatorio = new Relatorio();
        $this->usuarioTipoPagamento = new UsuarioTipoPagamento();
    }

    protected function login($usuario, $senha) {
        if ((isset($usuario)) && (isset($senha))) {
            $email = isset($_POST['usuario']) ? trim($_POST['usuario']) : false;
            $senha = isset($_POST['senha']) ? md5(trim(($_POST['senha']))) : false;

            $autenticacao = $this->usuario->autenticacaoUser($email, $senha);
            $autenticao_valida = $this->pegaAutenticacao($autenticacao, $email, $senha);
            return $autenticao_valida;
        } else {
            $this->view->load('login');
        }
    }

    protected function pegaAutenticacao($autenticacao, $email, $senha) {
        try {
            if ($autenticacao == true) {
                session_start();
                $usuario = $this->usuario->pegaLogado($email, $senha);
                $_SESSION['usuario'] = (int) $usuario;
                $this->home();
            } else {
                throw new Exception("Usuário ou Senha estão incorretos.");
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
            $this->view->load('login');
        }
    }

    protected function home() {
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {
            $id_usuario = (int) $_SESSION['usuario'];
            $dados['meses'] = $this->mesReferencia->listarMes();
            //$this->view->load('pagamento/listar_pagamento_mes', $dados);
            $this->view->load('home', $dados);
        } else {
            header('Location:index.php');
            session_destroy();
        }
    }

    protected function listarMesRef() {
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {
            try {
                $dados['meses'] = $this->mesReferencia->listarMes();
                $this->mesReferencia->pegaMesAutomatico();
                $this->view->load('mesReferencia/listar', $dados);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        } else {
            $this->mataSessao();
        }
    }

    protected function listarTP() {
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {
            $id_usuario = (int) $_SESSION['usuario'];
            $dados['tipoPagamentos'] = $this->tipoPagamentoDAO->listarTP($id_usuario);
            foreach ($dados['tipoPagamentos'] as $chave => $valor) {
                $td = $this->tipoDespesaDAO->buscar($valor['id_tipo_despesa']);
                $dados['tipoPagamentos'][$chave]['id_tipo_despesa'] = $td['descricao'];
            }
            $this->view->load('tipoPagamento/listar_tipo_pagamento', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function listarSP() {
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {
            $dados['statusPagamento'] = $this->statusPagamento->listar();
            $this->view->load('statusPagamento/listar_status_pagamento', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function listarTD() {
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {
            $id_user = $_SESSION['usuario'];
            $dados['tipoDespesa'] = $this->tipoDespesaDAO->listarTD($id_user);
            $this->view->load('tipoDespesa/listar', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function listaOrgaoPagador() {        
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {                        
            $dados['Orgao'] = $this->orgaoDAO->listarOP();
            $this->view->load('Orgao/listar', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function pegaClasse($classeAgora) {
        $classeAg = $this->classeNow->iniciarFabrica($classeAgora);
        return $classeAg;
    }

    protected function listarPagamentoMes($dados = Array()) {
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {
            $dados['meses'] = $this->mesReferencia->listarMes();
            $this->view->load('pagamento/listar_pagamento_mes', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function pegaListaPagamantoMes($mes_referencia, $usuario) {        
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {            
            $dados['pagamentos'] = $this->pagamentoDAO->listarPagamentoMes($mes_referencia, $usuario);            
            foreach ($dados['pagamentos'] as $ch => $value):
                $data_lancamento = $this->pagamento->ajustarData($value['data_lancamento']);
                $data_processamento = $this->pagamento->ajustarData($value['data_processamento']);
                $dados['pagamentos'][$ch]['data_lancamento'] = $data_lancamento;
                $dados['pagamentos'][$ch]['data_processamento'] = $data_processamento;
                $tipo_pag = $this->tipoPagamentoDAO->buscar($value['id_tipo_pagamento']);
                $dados['pagamentos'][$ch]['id_tipo_pagamento'] = $tipo_pag['descricao'];
                $mesref = $this->mesReferencia->buscar($value['id_mes_referencia']);
                $dados['pagamentos'][$ch]['id_mes_referencia'] = $mesref['descricao'];
                $usuario = $this->usuarioDAO->buscar($value['id_usuario']);
                $dados['pagamentos'][$ch]['id_usuario'] = $usuario['nome'];
                $status = $this->statusPagamento->buscar('id_status_pagamento', $value['id_status_pagamento']);
                $dados['pagamentos'][$ch]['id_status_pagamento'] = $status['chave'];
            endforeach;
                return $dados;
        } else {
            $this->mataSessao();
        }
    }

    protected function pegaMesRefNow() {
        /*Analisar a perda de sessão do id_mes_ref*/
        $this->iniciaSessao();              
        $mes_referencia = (int) $this->mesReferencia->pegaMesRef();        
        if ($mes_referencia != 0):
            $this->pagamentoDAO->buscar($mes_referencia);
        else:            
            $mes_referencia = (int) $_SESSION['id_mes_ref'];            
        endif;
        return $mes_referencia;
    }

    protected function listarPagamento() {
        $this->iniciaSessao();        
        if (isset($_SESSION['usuario'])) {
            $usuario = (int) $_SESSION['usuario'];            
            $mes_referencia = $this->pegaMesRefNow();
            $_SESSION['id_mes_ref'] = $mes_referencia;
            $dados = $this->pegaListaPagamantoMes($mes_referencia, $usuario);
            $id_usuario = $this->usuario->pegaIdLogado();
            $status_pg = $this->pagamentoDAO->pegaStatus('PG');
            $id_status_pg = (int) $status_pg['id_status_pagamento'];
            $total = $this->pagamentoDAO->SomarTotal($mes_referencia, $id_usuario, $id_status_pg);
            $subtotal = $this->pagamentoDAO->SomarSubtotal($mes_referencia, $id_usuario);
            $salario_extra = $this->pagamentoExtra->pegaSalarioExtra($mes_referencia, $id_usuario);
            $salario = $this->usuarioDAO->carregarSalario($id_usuario);
            $dados['total'] = $total;
            $dados['salario_extra'] = $salario_extra;
            $dados['salario'] = $salario["salario"] + $salario_extra;
            $dados['sobra'] = (double) ($dados['salario'] - $dados['total']);
            $dados['subtotal'] = $subtotal;
            $this->view->load('pagamento/listar_pagamento', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function processarPagamento($id) {
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {
            //print_r($_SESSION);      
            $id_usuario = $_SESSION['usuario'];
            if ($id) {
                $this->usuarioDAO->pegaSalario($id_usuario);
                $this->pagamento->processarPag($id);
                $this->listarPagamento();
            }
        } else {
            $this->mataSessao();
        }
    }

    protected function cancelarPagamento($id) {
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {
            //print_r($_SESSION);      
            $this->pagamento->cancelarPag($id);
            $this->listarPagamento();
        } else {
            $this->view->load('login');
            session_abort();
            session_destroy();
        }
    }

    protected function adicionarSalarioExtra() {
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {
            $id_mes = (int) $_REQUEST['id_mes_referencia'];
            if($_POST){
                $id_user = $_SESSION['usuario'];
                $valor_extra = $_POST['salario_extra'];
                $this->pagamentoExtra->inserirPagExtra($id_user, $id_mes, $valor_extra);
                $this->listarPagamentoMes();
                die();
            }
            $dados['meses'] = $this->mesReferencia->listarPagamentoMes($id_mes);            
            $this->view->load('mesReferencia/salarioExtra', $dados);
            
        } else {
            $this->mataSessao();
        }
    }

    protected function validarMesEmClonagem() {
        $this->iniciaSessao();
        $id_usuario = $_SESSION['usuario'];        
        $mes_ref = $_REQUEST['id_mes_referencia'];
        $processo = $this->pagamentoDAO->validandoMesReferenciaVazio($mes_ref,$id_usuario);        
        //corrigir validação
        if ($processo) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Clonar Pagamentos do Mês Anterior
     */

    protected function clonarPagamentoMes() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            try {                
                $msg = "";
                $MesClonadoVazio = $this->validarMesEmClonagem();                
                if ($MesClonadoVazio == true) {
                    $mes_ant = (int) $this->pegaMesAnterior();
                    $id_usuario = $_SESSION['usuario'];
                    $dados['id_mes_clone'] = $mes_ant;
                    $dados['id_usuario'] = $id_usuario;
                    $dados['id_mes_referencia'] = (int) $_REQUEST['id_mes_referencia'];
                    $processo = $this->pagamento->clonarPag($dados);
                    if ($processo == true) {
                        $msg = "clonagem efetuada com sucesso";
                    } 
                }else{
                    throw new Exception("Mês já encontra-se clonado.");
                }
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
            echo $msg;
            $this->listarPagamentoMes();
        } else {
            $this->mataSessao();
        }
    }

    protected function pegaMesAnterior() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            try {
                $id_mes_ref = (int) $_REQUEST['id_mes_referencia'];
                if (isset($id_mes_ref)) {
                    $mes_ant = $this->mesReferencia->pegaMesAnterior($id_mes_ref);
                    return $mes_ant;
                }
                throw new Exception('Falha ao referenciar mês!');
            } catch (Exception $exc) {
                echo $exc->getMessage() . '<br>';
            }
        } else {
            $this->mataSessao();
        }
    }

    protected function excluirPagamento($id) {
        $this->iniciaSessao();
        if (isset($_SESSION['usuario'])) {
            if ($id) {
                $this->pagamentoDAO->excluirPagamento(  $id);
                $this->listarPagamento();
            }
        } else {
            $this->mataSessao();
        }
    }

    protected function excluirMesRef($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($id) {
                $this->mesReferencia->excluir($tabela, 'id_mes_referencia', $id);
                $this->listarMesRef();
            }
        } else {
            $this->mataSessao();
        }
    }

    protected function inserirOP() {  
        
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            try {
                if (isset($_POST['descricao']) && !empty($_POST['descricao'])) {
                    $orgaoPagadorDAO = new OrgaoDAO();
                    $orgaoPagadorDAO->inserirOP(new Orgao($_POST['chave'],$_POST['descricao']));
                    $dados['Orgao'] = $orgaoPagadorDAO->listarOP();
                    $this->view->load("Orgao/listar", $dados);
                    die();
                }
                    $this->view->load('Orgao/inserir');
                
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        } else {
            $this->mataSessao();
        }
    }

    protected function editarOP($id) {        
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            try{
                if ($_POST) {
                    $orgaoPagadorDAO = new OrgaoDAO();
                    $orgaoPagadorDAO->alterar($id, 'id_orgao_pagador', $_POST);
                    $this->listaOrgaoPagador();
                    die();
                }
                $orgaoPagadorDAO = new OrgaoDAO();
                $dados = $orgaoPagadorDAO->buscar('id_orgao_pagador', $id);
                $this->view->load('Orgao/alterar', $dados);

            }catch(Exception $msg){
                echo $msg->getMessage();
            }
        } else {
            $this->mataSessao();
        }
    }

    /* EXCLUIR INICIO */

    protected function excluirOP($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            try {
                if (isset($id)) {
                    $orgaoPagadorDAO = new OrgaoDAO();
                    $orgaoPagadorDAO->excluir( $id);
                    $dados['Orgao'] = $orgaoPagadorDAO->listarOP();
                    $this->view->load("Orgao/listar", $dados);
                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        } else {
            $this->mataSessao();
        }
    }

    protected function excluirTP($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $this->tipoPagamentoDAO->excluir($id);            
            $this->listarTP();
        } else {
            $this->mataSessao();
        }
    }

    protected function excluirSP($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $this->statusPagamento->excluir('id_status_pagamento', $id);
            $this->home();
        } else {
            $this->mataSessao();
        }
    }

    public function excluirTD($id) {        
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $this->tipoDespesaDAO->excluir($id);
            $this->listarTD();
        } else {
            $this->mataSessao();
        }
    }

    protected function excluirPagamentoIR($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $this->pagamentoIR->excluir('id_ir', $id);
            $this->listarPagamentoIR();
        } else {
            $this->mataSessao();
        }
    }

    protected function excluirTipoSaldo($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $this->tipoSaldo->excluir('id_tipo_saldo', $id);
            $dados['tipoSaldo'] = $this->tipoSaldo->listar();
            $this->home();
        } else {
            $this->mataSessao();
        }
    }

    /* EXCLUIR FIM */

    protected function inserirTipoPagamento() {
        $this->iniciaSessao();
        $id_user = $_SESSION['usuario'];
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $dados['descricao'] = $_POST['descricao'];
                $dados['id_tipo_despesa'] = $_POST['id_tipo_despesa'];
                $tipoPagamento = new TipoPagamento($dados['descricao'], $dados['id_tipo_despesa']);
                $salvarTP = $this->tipoPagamentoDAO->inserirTP($tipoPagamento);
                //var_dump($salvarTP);die();
                if ($salvarTP) {
                    $idTP = $this->tipoPagamentoDAO->recuperarIdTP();

                    $this->usuarioTipoPagamento->inserirUTP($id_user, $idTP);
                }
                $this->listarTP();
                die();
            }
            $dados['tipo_despesa'] = $this->tipoDespesaDAO->listarTD($id_user);
            $this->view->load('tipoPagamento/inserir', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function inserirSP() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $this->statusPagamento->inserir($_POST);
                $this->home();
            }
            $this->view->load('statusPagamento/inserir');
        } else {
            $this->mataSessao();
        }
    }

    protected function inserirTD() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $id_user = $_SESSION['usuario'];
                $ch = $_POST['chave'];
                $desc = $_POST['descricao'];
                $tipoDespesa = new TipoDespesa($id_user,$ch,$desc);
                $this->tipoDespesaDAO->inserirTD($tipoDespesa);
                $this->listarTD();
                die();
            }            
            $this->view->load('tipoDespesa/inserir');
        } else {
            $this->mataSessao();
        }
    }

    protected function alterarSP($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $this->statusPagamento->alterar($id, 'id_status_pagamento', $_POST);
                $this->home();
            }
            $dados = $this->statusPagamento->buscar('id_status_pagamento', $id);
            $this->view->load('statusPagamento/editar', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function alterarTD($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $chave = $_POST['chave'];
                $descricao = $_POST['descricao'];
                $this->tipoDespesaDAO->alterar($id, $chave, $descricao);
                $this->listarTD();
                die();
            }
            $dados = $this->tipoDespesaDAO->buscar($id);
            $this->view->load('tipoDespesa/alterar', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function alterarTP($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $objTP = $this->tipoPagamentoDAO;
            $tipo_despesa = $this->tipoDespesaDAO;
            if ($_POST) {
                $objTP->alterar($id, 'id_tipo_pagamento', $_POST);
                $this->listarTP();
                die();
            }
            $dados['tp'] = $objTP->buscar($id);
            $dados['td'] = $tipo_despesa->listar();
            $this->view->load('tipoPagamento/editar', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function inserirTipoSaldo() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $this->tipoSaldo->inserir($_POST);
                $this->home();
            }
            $this->view->load('tipoSaldo/inserir');
        } else {
            $this->mataSessao();
        }
    }

    protected function alterarTipoSaldo($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $this->tipoSaldo->alterar($id, 'id_tipo_saldo', $_POST);
                header("Location:home.php");
            }
            $dados = $this->tipoSaldo->buscar('id_tipo_saldo', $id);
            $this->view->load('tipoSaldo/editar', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function inserirSalarioMes() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if (isset($_POST['liquido']) && !empty($_POST['liquido'])) {
                $this->salarioMensal->inserirSalarioMes($_POST);
                $dados['salarioMes'] = $this->salarioMensal->listar();

                for ($i = 0; $i < count($dados['salarioMes']); $i++) {
                    $orgao = $this->Orgao->buscarOP($dados['salarioMes'][$i]['id_orgao_pagador']);

                    $dados['salarioMes'][$i]['orgao'] = $orgao["descricao"];
                }

                $this->view->load('salarioMes/listar', $dados);
            }
            $dados = $this->Orgao->listar();
            $this->view->load("salarioMes/inserir", $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function inserirUsuario() {
        
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $dados = array();
            if ($_POST) {
                if (isset($_POST['nome']) && $_POST['nome'] != '') {
                    $dados['nome'] = $_POST['nome'];
                }if (isset($_POST['sobrenome']) && $_POST['sobrenome'] != '') {
                    $dados['sobrenome'] = $_POST['sobrenome'];
                }if (isset($_POST['email']) && $_POST['email'] != '') {
                    $dados['email'] = $_POST['email'];
                }if (isset($_POST['senha']) && $_POST['senha'] != '') {
                    $dados['senha'] = md5($_POST['senha']);
                }
                $dados['salario'] = $this->AjusteReal($_POST['salario']);
                $dados['id_orgao_pagador'] = $_POST['id_orgao_pagador'];
                $dados['id_status_usuario'] = $_POST['id_status_usuario'];

                $usuario = new Usuario($dados['nome'],$dados['sobrenome'],
                                                    $dados['salario'],$dados['id_orgao_pagador'],
                                                    $dados['id_status_usuario']);
                
                $autenticacaoUsuario = new AutenticacaoUsuario($dados['email'],$dados['senha']);
                $this->usuarioDAO->inserirUsuario($usuario,$autenticacaoUsuario);
                $dados['user'] = $this->usuarioDAO->listar();

                for ($i = 0; $i < count($dados['user']); $i++) {                    
                    $fp = $this->orgaoDAO->buscar('id_orgao_pagador', $dados['user'][$i]['id_orgao_pagador']);
                    $dados['user'][$i]['id_orgao_pagador'] = $fp['descricao'];
                }

                $this->view->load('usuario/lista_usuario', $dados);
                die();
            }            
            $dados['OrgaoPagador'] = $this->orgaoDAO->listarOP();
            $dados['StatusUsuario'] = $this->statusUsuario->listar();
            $this->view->load('usuario/inserir_usuario', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function AjusteReal($valor) {        
        $salario = str_replace('.', '', $valor);
        $salario_tratado = str_replace(',', '.', $salario);
        return $salario_tratado;
    }

    protected function inserirPagamento() {        
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $_POST['id_usuario'] = $_SESSION['usuario'];
                $mes_ref = $_SESSION['id_mes_ref'];
                $_POST['valor_pagamento'] = $this->AjusteReal($_POST['valor_pagamento']);                
                $pagamento = new Pagamento($_POST['id_tipo_pagamento'],$_POST['valor_pagamento'],$_POST['data_lancamento'],$_POST['id_usuario'],$_POST['id_status_pagamento'],$_POST['id_mes_referencia'],$_POST['ch_clone']);
                $salvar_pag = $this->pagamentoDAO->inserir($pagamento);
                if ($salvar_pag) {
                    echo 'Tipo Pagamento incluído com Sucesso!';
                }
                $this->listarPagamento();
                die();
            }
            $id_user = $_SESSION['usuario'];
            $status_nao_pago = $this->statusPagamento->pegaStatus('NPG');
            $mes_ref = $this->mesReferencia->buscar($_SESSION['id_mes_ref']);
            $dados['mes_ref'] = $mes_ref;
            $dados['id_tipo_pagamento'] = $this->tipoPagamentoDAO->listarTP($id_user);
            $dados['id_usuario'] = $this->usuarioDAO->listar();
            $dados['id_status_pagamento'] = $status_nao_pago[0];
            $dados['data_lancamento'] = date('Y-m-d');
            $dados['ch_clone'] = 'S';
            $this->view->load('pagamento/inserir', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function alterarPagamento($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {            
            if (isset($id)) {                
                if($_POST){                    
                    $_POST['valor_pagamento'] = $this->AjusteReal($_POST['valor_pagamento']);                    
                    $this->pagamento->alterarPagamento($id, 'id_pagamento', $_POST);                    
                    $this->listarPagamento();
                    die();
                }
                $dados['id_mes_ref'] = $_SESSION['id_mes_ref'];
                $dados['pagamento'] = $this->pagamento->buscar('id_pagamento', $id);
                $dados['tipo_pagamento'] = $this->tipoPagamento->listar();
                $dados['usuario'] = $this->usuario->listar();
                $dados['data_lancamento'] = date('Y-m-d');
                $dados['id_status_pagamento'] = 4;                  
                $dados['valor_pagamento'] = $this->AjusteReal($_POST['valor_pagamento']);
                $this->view->load('pagamento/editar', $dados);
        }
        } else {
            $this->mataSessao();
        }
    }

    protected function listarUsuario() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {            
            $dados['user'] = $this->usuarioDAO->listar();
            for ($i = 0; $i < count($dados['user']); $i++) {                
                $op = $this->orgaoDAO->buscar('id_orgao_pagador', $dados['user'][$i]['id_orgao_pagador']);
                $dados['user'][$i]['id_orgao_pagador'] = $op["descricao"];
            }
            $this->view->load('usuario/lista_usuario', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function listarRelatorio() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $dados = $this->relatorio->gerarRelatorio();

            $this->view->load('relatorios/listar_relatorios', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function excluirUsuario($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {            
            $userDel = $this->usuarioDAO->excluirUsuario($id);
            //var_dump($userDel);die();
            if ($userDel) {       
                $usuarioAutenticacaoDAO = new AutenticacaoUsuarioDAO();         
                $usuarioAutenticacaoDAO->excluirUserAutenticacao($id);
            }
            $dados['user'] = $this->usuarioDAO->listar();
            for ($i = 0; $i < count($dados['user']); $i++) {                
                $op = $this->orgaoDAO->buscar('id_orgao_pagador', $dados['user'][$i]['id_orgao_pagador']);
                $dados['user'][$i]['id_orgao_pagador'] = $op["descricao"];
            }
            $this->view->load('usuario/lista_usuario', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function alterarUsuario($id) {        
        $this->iniciaSessao();        
        if ($_SESSION['usuario']) {            
            $usuarioAutenticacaoDAO = new AutenticacaoUsuarioDAO();            
            if ($_POST) {                
                $_POST['salario'] = str_replace(",",".",$_POST['salario']);                
                $usuario = new Usuario($_POST['nome'],$_POST['sobrenome'],$_POST['salario'],$_POST['id_orgao_pagador'],$_POST['id_status_usuario']);
                
                $alterarUser = $this->usuarioDAO->alterarUsuario($id, $usuario);
                if ($alterarUser) {
                    $email = $_POST['email'];
                    $senha = md5($_POST['senha']);
                    $usuarioAutenticacaoDAO->alterarUserAut($email, $senha, $id);
                }                
                $this->listarUsuario();
                die();
            }            
            $dados['user'] = $this->usuarioDAO->buscar($id);
            $dados['autenticacao'] = $usuarioAutenticacaoDAO->buscarUsuarioAutenticacao($id);
            $dados['op'] = $dados['user']['id_orgao_pagador'];            
            $dados['OrgaoPagador'] = $this->orgaoDAO->listarOP();
            $dados['StatusUsuario'] = $this->statusUsuario->listar();
            $this->view->load('usuario/editar_usuario', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function listarStatusUsuario() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $dados['statusUsuario'] = $this->statusUsuario->listar();
            $view = $this->view->load('statusUsuario/listar', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function inserirMesRef() {        
        $this->listarMesRef();

        //$this->view->load('mesReferencia/inserir');
    }

    protected function inserirSU() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $this->statusUsuario->inserir($_POST);
                $this->home();
            }
            $this->view->load('statusUsuario/inserir');
        } else {
            $this->mataSessao();
        }
    }

    protected function alterarSU($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $this->statusUsuario->alterar($id, 'id_status_usuario', $_POST);
                $this->home();
            }
            $dados = $this->statusUsuario->buscar('id_status_usuario', $id);

            $this->view->load('statusUsuario/editar', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function alterarMesRef($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            if ($_POST) {
                $this->mesReferencia->alterar($id, 'id_mes_referencia', $_POST);
                $this->home();
            }
            $dados = $this->mesReferencia->buscar('id_mes_referencia', $id);

            $this->view->load('mesReferencia/alterar', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function listarPagamentoIR() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $id_usuario = $_SESSION['usuario'];
            $dados['listarIR'] = $this->pagamentoIR->listarPagIr($id_usuario);
            $this->view->load('pagamentoIR/listar_pagamento_ir', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function inserirPagamentoIR() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $id_usuario = $_SESSION['usuario'];
            $dados['tipo_despesa'] = $this->tipoDespesa->listar();
            $data_proc = date('Y-m-d:H:m:s');
            if ($_POST) {
                $this->pagamentoIR->inserirPagamentoIR($id_usuario, $_POST['id_tipo_despesa'], $_POST['val_desc'], $_POST['valor'], $_POST['img_ir'], $data_proc);
            }

            $this->view->load('pagamentoIR/inserir', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function excluirSU($id) {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $this->statusUsuario->excluir('id_status_usuario', $id);
            $this->home();
        } else {
            $this->mataSessao();
        }
    }

    final private function iniciaSessao() {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    final private function mataSessao() {
        session_destroy();
        session_abort();
        return header('Location:index.php');
    }

}
