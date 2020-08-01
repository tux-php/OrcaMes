<!DOCTYPE html>
<html>
   <head>
      <link rel="stylesheet" href="././css/bootstrap.min.css" />
      <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
      <!--<script type="text/javascript" src="././js/bootstrap.min.js"></script> -->
      <script type="text/javascript" src="././jquery/jquery.maskMoney.min.js"></script>
      <script type="text/javascript" src="././js/efeitos.js"></script>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
   <body>
      <form class="form-horizontal" action="" method="post" >
         <div class="container-fluid">
            <div class="panel-info">
               <div class="panel-heading text-uppercase">
                  <h4>Alterar Pagamento</h4>
               </div>
            </div>
         </div>
         <div class="container-fluid">
            <div class="panel panel-default">
               <div class="panel-body">
                  <div class="form-group">
                     <label for="nome"  class="col-sm-2 control-label">Usuário:</label>
                     <div class="col-sm-3">
                        <select name="id_usuario" id=""class="form-control">
                           <?php
                              $id_user = $dados['pagamento']['id_usuario'];
                              foreach ($dados['usuario'] as $user) {
                                  if ($user['id_usuario'] == $id_user) { ?>                                
                           <option value="<?php echo $user['id_usuario'] ?>"><?= $user['nome']; ?></option>
                           <?php }} ?>
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="nome"  class="col-sm-2 control-label">Tipo Pagamento:</label>
                     <div class="col-sm-3">
                        <select class="form-control" name="id_tipo_pagamento" id="">
                           Selecione Tipo Pagamento:
                           <?php
                              $id_tp = $dados['pagamento']['id_tipo_pagamento'];
                                  foreach ($dados['tipo_pagamento'] as $tp) {
                                      if ($tp['id_tipo_pagamento'] == $id_tp) {
                                          ?>
                           <option value="<?php echo $tp['id_tipo_pagamento'] ?>" name="id_tipo_pagamento"><?= $tp['descricao']; ?></option>
                           <?php }}?>
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="valor"  class="col-sm-2 control-label">Valor(R$):</label>
                     <div class="col-sm-3">
                        
                        <input type="text" class="form-control" name="valor_pagamento"  required="" id="valor_pagamento" placeholder="R$.." value="<?= $dados['pagamento']['valor_pagamento']; ?>">
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div>
            <input type="hidden" name="data_lancamento" value="<?= $dados['data_lancamento']; ?>" />
            <input type="hidden" name="id_status_pagamento" value="<?= $dados['id_status_pagamento']; ?>" />                
         </div>
         </div>
         <div class="col-sm-offset-5 col-sm-10">
            <a class="btn btn-primary text-uppercase text-center" role="button" href="?action=detalharPagamentoMes&id_mes_referencia=<?=$dados['id_mes_ref']?>">Voltar</a>                
            <input class="btn btn-success text-uppercase text-center" role="button" type="submit" value="Salvar" />
         </div>
         </div>
      </form>
   </body>
</html>