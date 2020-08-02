<!DOCTYPE HTML>
<html lang="en-US">
   <head>
      <link rel="stylesheet" href="././css/bootstrap.min.css" />
      <!--<link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />-->
      <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
      <script type="text/javascript" src="././js/bootstrap.min.js"></script> 
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title></title>
   </head>
   <body>
      <div class="panel panel-info">
         <div class="panel-heading text-uppercase" ><strong>Tipos de Pagamentos</strong> </div>
      </div>
      <div class="table-responsive">
         <table class="table table-striped table-hover table-condensed">
            <thead>
               <tr>
                  <th scope="col">#</th>
                  <th scope="col" class="text-uppercase">Descrição</th>
                  <th scope="col" class="text-uppercase">Tipo de Despesa</th>
                  <th scope="col" colspan="2" class="text-uppercase text-center">Ações</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  if ($dados['tipoPagamentos']) {
                      foreach ($dados['tipoPagamentos'] as $ch=>$tp) {
                      //var_dump($tp);
                      ?>
               <tr>
                  <th scope="row"><?= $ch;?></th>
                  <td class="text-uppercase"><?= $tp['descricao'] ?></td>
                  <td class="text-uppercase"><?= $tp['id_tipo_despesa'] ?></td>
                  <td class="text-center">                    
                     <a class="btn-sm btn-primary text-uppercase text-center" role="button" href="?action=alterarTP&id_tipo_pagamento=<?php echo $tp['id_tipo_pagamento'] ?>">Alterar</a>
                     <a class="btn-sm btn-danger text-uppercase text-center" role="button"  href="?action=excluirTP&id_tipo_pagamento=<?php echo $tp['id_tipo_pagamento'] ?>">Excluir</a>
                  </td>
               </tr>
               <?php }} ?>
            </tbody>
         </table>
      </div>
      <div class="col-sm-offset-5 col-sm-10">
         <a class="btn btn-primary text-uppercase text-center" role="button" href="?action=home">VOLTAR</a>
         <a class="btn btn-success text-uppercase text-center" role="button" href="?action=inserirTipoPagamento">Novo</a>
      </div>
   </body>
</html>