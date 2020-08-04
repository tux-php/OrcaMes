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
   <?php
        include("./menu.php");
    ?>
      <form class="form-horizontal" action="" method="post">
         <div class="container-fluid">
            <div class="panel-info">
               <div class="panel-heading text-uppercase">
                  <h4>Alterar Instituição Patrocinadora</h4>
               </div>
            </div>
         </div>
         <div class="container-fluid">
            <div class="panel panel-default">
               <div class="panel-body">
                  <div class="form-group">
                     <label for="chave"  class="col-sm-2 control-label text-uppercase">chave:</label>
                     <div class="col-sm-3">                        
                        <input type="text" class="form-control text-uppercase" autofocus="" name="chave"  required="" id="chave" value="<?php echo $dados['chave'];?>">
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="descricao"  class="col-sm-2 control-label text-uppercase">descricao:</label>
                     <div class="col-sm-3">                        
                        <input type="text" class="form-control text-uppercase" name="descricao"  required="" id="descricao" value="<?php echo $dados['descricao'];?>">
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-sm-offset-5 col-sm-10">
               <a class="btn btn-primary text-uppercase text-center" role="button" href="index.php?action=listaOrgaoPagador">Voltar</a>                
               <input class="btn btn-success text-uppercase text-center" role="button" type="submit" value="Salvar" />
            </div>
         </div>
      </form>
   </body>
</html>