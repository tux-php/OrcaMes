<?php
class Controle extends Acao {    
    private Orgao $orgao;
    private OrgaoDAO $orgaoDAO;
    private Usuario $usuario;
    private UsuarioDAO $usuarioDAO;
    private TipoPagamento $tipoPagamento;
    private TipoPagamentoDAO $tipoPagamentoDAO;    
    private Pagamento $pagamento;
    private PagamentoDAO $pagamentoDAO;
    private PagamentoExtra $pagamentoExtra;
    private PagamentoExtraDAO $pagamentoExtraDAO;
    private $pagamentoIR;
    private $view;
    private StatusPagamento $statusPagamento;
    private StatusPagamentoDAO $statusPagamentoDAO;
    private StatusUsuario $statusUsuario;
    private StatusUsuarioDAO $statusUsuarioDAO;
    private $mesReferencia;
    private TipoDespesa $tipoDespesa;
    private TipoDespesaDAO $tipoDespesaDAO;
    private $despesa;
    private Relatorio $relatorio;
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
        $this->pagamento = new Pagamento();
        $this->pagamentoDAO = new PagamentoDAO();
        $this->pagamentoExtra = new PagamentoExtra();
        $this->pagamentoExtraDAO = new PagamentoExtraDAO();
        $this->pagamentoIR = new PagamentoIR();
        $this->view = new View();
        $this->statusPagamento = new StatusPagamento();
        $this->statusPagamentoDAO = new StatusPagamentoDAO();
        $this->statusUsuario = new StatusUsuario();
        $this->statusUsuarioDAO = new StatusUsuarioDAO();
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
                throw new Exception("<strong>Atenção!</strong> Usuário ou Senha estão incorretos.");
            }
        } catch (Exception $exc) {            
            echo "<div class='alerta error'>".$exc->getMessage()."</div>";
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
                $this->mesReferencia->pegarMesAutomatico();
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
            $status_pg = $this->statusPagamentoDAO->pegaStatus('PG');
            $id_status_pg = (int) $status_pg['id_status_pagamento'];
            $total = $this->pagamentoDAO->SomarTotal($mes_referencia, $id_usuario, $id_status_pg);            
            $subtotal = $this->pagamentoDAO->SomarSubtotal($mes_referencia, $id_usuario);
            $salario_extra = $this->pagamentoExtraDAO->pegaSalarioExtra($mes_referencia, $id_usuario);
            $salario = $this->usuarioDAO->carregarSalario($id_usuario);
            $dados['total'] = $total;
            $dados['salario_extra'] = $salario_extra;
            $dados['salario'] = $salario["salario"] + $salario_extra;
            $dados['sobra'] = (double) ($dados['salario'] - $dados['total']);
            $dados['subtotal'] = $subtotal;     
            /*echo"<pre>";
            var_dump($dados);
            echo"</pre>";
            die();*/
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
                $pagamento_extra = new PagamentoExtra($id_user,$id_mes,$valor_extra);                
                $salvar = $this->pagamentoExtraDAO->inserirPagExtra($pagamento_extra);
                if($salvar):
                    echo Mensagem::dispararSucesso("Valor R$ $valor_extra inserido com sucesso");
                else:
                    echo Mensagem::dispararErro("Falha ao inserir R$ $valor_extra!");
                endif;
                $this->listarPagamentoMes();
                exit;
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
                $MesClonadoVazio = $this->validarMesEmClonagem();                                
                if ($MesClonadoVazio == true) :
                    $mes_ant = (int) $this->pegaMesAnterior();
                    $id_usuario = $_SESSION['usuario'];
                    $dados['id_mes_clone'] = $mes_ant;
                    $dados['id_usuario'] = $id_usuario;
                    $dados['id_mes_referencia'] = (int) $_REQUEST['id_mes_referencia'];
                    $processo = $this->pagamento->clonarPag($dados);                    
                    if ($processo == true):                        
                        echo Mensagem::dispararSucesso("Clonagem efetuada com sucesso!");
                    endif;
                else:                    
                    echo Mensagem::dispararErro("Mês já <strong>encontra-se clonado!</strong>");
                endif;
                $this->listarPagamentoMes();
                
            } catch (Exception $exc) {
                echo Mensagem::dispararErro($exc->getMessage());
            }   
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
                    $salvar = $orgaoPagadorDAO->inserirOP(new Orgao($_POST['chave'],$_POST['descricao']));
                    if($salvar):
                        echo Mensagem::dispararSucesso("Órgão inserido com sucesso!");
                    else:
                        echo Mensagem::dispararErro("Órgão não inserido!");
                    endif;
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
                    $alterar = $orgaoPagadorDAO->alterar($id, 'id_orgao_pagador', $_POST);
                    if($alterar):
                        echo Mensagem::dispararSucesso("Órgão alterado com sucesso!");
                    else:
                        echo Mensagem::dispararErro("Órgão não alterado!");
                    endif;
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
                    $apagaOP = $orgaoPagadorDAO->excluir($id);
                    if($apagaOP):                        
                        echo Mensagem::dispararSucesso("Órgão excluído com sucesso!");
                    $dados['Orgao'] = $orgaoPagadorDAO->listarOP();
                    $this->view->load("Orgao/listar", $dados);
                    else:
                        echo Mensagem::dispararErro("Órgão não excluído!");
                    endif;
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
            $apagaTP = $this->tipoPagamentoDAO->excluir($id);            
            if($apagaTP):
                echo Mensagem::dispararSucesso("Tipo pagamentos excluído com sucesso!");                
            else:
                echo Mensagem::dispararErro("Tipo pagamento não excluído!");
            endif;
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
                if ($salvarTP):
                    $idTP = $this->tipoPagamentoDAO->recuperarIdTP();
                    $this->usuarioTipoPagamento->inserirUTP($id_user, $idTP);                
                    echo Mensagem::dispararSucesso("Tipo de pagamento inserido com sucesso!");
                else:
                    echo Mensagem::dispararErro("Tipo de pagamento não inserido!");
                endif;
                $this->listarTP();
                exit;
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
                $salvar = $this->tipoDespesaDAO->inserirTD($tipoDespesa);
                if($salvar):
                    echo Mensagem::dispararSucesso("Tipo Despesa inserida com sucesso!");
                else:
                    echo Mensagem::dispararErro("Tipo Despesa não inserida!");
                endif;
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
                $alteraTD = $this->tipoDespesaDAO->alterar($id, $chave, $descricao);
                if($alteraTD):
                    echo Mensagem::dispararSucesso("Tipo de Despesa alterada com sucesso!");                    
                else:
                    echo Mensagem::dispararErro("Tipo de Despesa não alterada!");
                endif;
                $this->listarTD();
                exit;
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
                $alterar = $objTP->alterar($id, 'id_tipo_pagamento', $_POST);
                if($alterar):
                    echo Mensagem::dispararSucesso("Tipo Pagamento alterado com sucesso!");
                else:
                    echo Mensagem::dispararErro("Tipo Pagamento não alterado!");
                endif;
                $this->listarTP();
                die();
            }
            $dados['tp'] = $objTP->buscar($id);
            $dados['td'] = $tipo_despesa->listarTD();
            $this->view->load('tipoPagamento/editar', $dados);
        } else {
            $this->mataSessao();
        }
    }

    /*protected function inserirSalarioMes() {
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
    }*/

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
                $salvar = $this->usuarioDAO->inserirUsuario($usuario,$autenticacaoUsuario);
                if($salvar):
                    echo Mensagem::dispararSucesso("Usuário inserido com sucesso!");
                else:
                    echo Mensagem::dispararErro("Usuário não inserido!");
                endif;
                $dados['user'] = $this->usuarioDAO->listar();

                for ($i = 0; $i < count($dados['user']); $i++) {                    
                    $fp = $this->orgaoDAO->buscar('id_orgao_pagador', $dados['user'][$i]['id_orgao_pagador']);
                    $dados['user'][$i]['id_orgao_pagador'] = $fp['descricao'];
                }

                $this->view->load('usuario/lista_usuario', $dados);
                die();
            }            
            $dados['OrgaoPagador'] = $this->orgaoDAO->listarOP();
            $dados['StatusUsuario'] = $this->statusUsuarioDAO->listar();
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
                    echo Mensagem::dispararSucesso("Novo pagamento inserido com sucesso!");
                }else{
                    echo Mensagem::dispararErro("Pagamento não inserido!");
                }
                $this->listarPagamento();
                die();
            }
            $id_user = $_SESSION['usuario'];
            $status_nao_pago = $this->statusPagamentoDAO->pegaStatus('NPG');
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
                    $salvar_pag = $this->pagamentoDAO->alterarPagamento($id,$_POST);
                    if ($salvar_pag):
                        echo Mensagem::dispararSucesso("Pagamento alterado com sucesso!");                                           
                    else:
                    echo Mensagem::dispararErro("Pagamento não alterado!");
                    endif;  
                    $this->listarPagamento();
                    die();
                }                
                $dados['id_mes_ref'] = $_SESSION['id_mes_ref'];
                $dados['pagamento'] = $this->pagamentoDAO->buscar($id);
                $dados['tipo_pagamento'] = $this->tipoPagamentoDAO->listar();
                $dados['usuario'] = $this->usuarioDAO->listar();
                $dados['data_lancamento'] = date('Y-m-d');
                $dados['id_status_pagamento'] = 4;                  
                $dados['valor_pagamento'] = @$_POST['valor_pagamento'];
                
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
                    $alterar = $usuarioAutenticacaoDAO->alterarUserAut($email, $senha, $id);
                    if($alterar):
                        echo Mensagem::dispararSucesso("Usuário alterado com sucesso!");
                    else:
                        echo Mensagem::dispararErro("Usuário não alterado!");
                    endif;
                }                
                $this->listarUsuario();
                die();
            }            
            $dados['user'] = $this->usuarioDAO->buscar($id);
            $dados['autenticacao'] = $usuarioAutenticacaoDAO->buscarUsuarioAutenticacao($id);
            $dados['op'] = $dados['user']['id_orgao_pagador'];            
            $dados['OrgaoPagador'] = $this->orgaoDAO->listarOP();
            $dados['StatusUsuario'] = $this->statusUsuarioDAO->listar();
            $this->view->load('usuario/editar_usuario', $dados);
        } else {
            $this->mataSessao();
        }
    }

    protected function listarStatusUsuario() {
        $this->iniciaSessao();
        if ($_SESSION['usuario']) {
            $dados['statusUsuario'] = $this->statusUsuarioDAO->listar();
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
                $statusUsuario = new StatusUsuario($_POST['chave'],$_POST['descricao']);
                $this->statusUsuarioDAO->inserir($statusUsuario);
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
                $this->statusUsuarioDAO->alterar($id, 'id_status_usuario', $_POST);
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
            $this->statusUsuarioDAO->excluir('id_status_usuario', $id);
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
