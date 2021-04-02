<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />    
        <link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />   
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
    </head>
    <body>  
        <?php
            include("./menu.php");
        ?>
        <div class="container-fluid">
            <div class="panel-info">
            <div class="panel-heading text-uppercase"><strong>Lista de Pagamentos</strong></div>
        </div>
            <div class="table-responsive">
                    <table class="table table-hover table-striped table-condensed">
                        <thead>
                            <tr>
                                <th scope="col" class="text-uppercase text-left">#</th>
                                <th scope="col" class="text-uppercase text-left">Tipo Pagamento</th>
                                <th scope="col" class="text-uppercase text-left">Valor</th>
                                <th scope="col" class="text-uppercase text-center">Lançamento</th>
                                <th scope="col" class="text-uppercase text-center">Processamento</th>
                                <th scope="col" class="text-uppercase text-center">Mes Referencia</th>
                                <th scope="col" class="text-uppercase text-center">Responsável</th>
                                <th scope="col" class="text-uppercase text-center" colspan="4">Ações</th>
                                <th scope="col" class="text-uppercase text-center">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                <?php 
                $salario = $dados['salario'];
                $total = $dados['total'];
                $sobra = $dados['sobra'];
                $subtotal = $dados['subtotal'];
                    if ($dados) :    
                            foreach ($dados['pagamentos'] as $ch=>$dados):?>
                            
                                <tr>
                                    <th scope="row"><?=$ch;?></th>
                                    <td class="text-left"><?=$dados['tipo_pagamento'];?></td>
                                    <td class="text-left"><strong>R$ <?=str_replace('.',',',$dados['valor_pagamento']);?></strong></td>
                                    <td class="text-center"><?=$dados['data_lancamento'];?></td>
                                    <td class="text-center"><?php
                                if (isset($dados['data_processamento']) && $dados['data_processamento']) {
                                            echo $dados['data_processamento'];?>
                                    </td>
                                <?php
                                } else {echo '--';}?>
                                    <td class="text-center text-capitalize"><?=$dados['mes_referencia'];?></td>
                                    <td class="text-center text-capitalize"><?=$dados['nome'];?></td>
                                    <td class="text-center">
                                        <a href="?action=alterarPagamento&id_pagamento=<?php echo $dados['id_pagamento'] ?>" title="Editar"><span class="glyphicon glyphicon-cog" style="color: #777"></span></a>
                                    </td>
                                    <td class="text-center">
                                        <a href="?action=excluirPagamento&id_pagamento=<?php echo $dados['id_pagamento']; ?>" title="Excluir"><span class="glyphicon glyphicon-remove" style="color: #777"></span></a>
                                    </td>
                                    <td class="text-center"><a href="?action=processarPagamento&id_pagamento=<?php echo $dados['id_pagamento']; ?>" title="Processar Pagamento" id="processar" rel="<?=$dados['valor_pagamento'];?>"><span class="glyphicon glyphicon-thumbs-up" style="color: #777"></span></a></td>
                                    <td class="text-center"><a href="?action=cancelarPagamento&id_pagamento=<?php echo $dados['id_pagamento']; ?>" title="Cancelar Pagamento" id="cancelar" rel="<?=$dados['valor_pagamento'];?>"><span class="glyphicon glyphicon-ban-circle" style="color: #777"></span></a></td>
                                    <td class="text-center"><?=$dados['status_pgt'] == 'PG' ? '<span class="label label-success" id="pag_sucesso">pagamento efetuado</span>' : '<span class="label label-danger" id="pag_nao_processado">pagamento pendente </span>';?></td>
                                </tr>
                            <?php
                        endforeach;
                    endif;
?>
                        </tbody>
                    </table>
                </div>         

<div class="btn-group btn-group-justified  flex-wrap" role="group" aria-label="Grupos de valores">
  <div class="btn-group" role="group">
    <button type="button" class="btn btn-primary"><h4>Salário:<?=$salario;?></h4></button>    
  </div>
  <div class="btn-group" role="group">
    <button type="button" class="btn btn-success"><h4>Processado:R$ <?=$total;?></h4></button>
  </div>
  <div class="btn-group" role="group">
    <button type="button" class="btn btn-info"><h4>Sobra:R$ <?=$sobra;?></h4></button>
  </div> 
</div>
<div class="btn-group btn-group-justified flex-wrap" role="group" aria-label="Grupos de valores">
<div class="btn-group" role="group">
    <button type="button" class="btn btn-warning"><h4>Subtotal:R$ <?=$subtotal;?></h4></button>
  </div>
</div>
<br />
        <div class="col-sm-offset-5 col-sm-10">           
            <a class="btn btn-primary text-uppercase text-center" role="button" href="?action=listarPagamentoMes" title="voltar">Voltar</a>
            <a class="btn btn-success text-uppercase text-center" role="button" href="?action=inserirPagamento" title="Novo Pagamento">Novo</a>
            </div>
    </body>
</html>