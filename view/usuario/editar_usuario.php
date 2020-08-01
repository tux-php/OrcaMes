<!DOCTYPE html>
<html>
   <head>
      <link rel="stylesheet" href="././css/bootstrap.min.css" />
      <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
      <!--<script type="text/javascript" src="././js/bootstrap.min.js"></script> -->
      <!--<script type="text/javascript" src="././jquery/jquery.maskMoney.min.js"></script>
      <script type="text/javascript" src="././js/efeitos.js"></script>-->
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
   <body>
      <form class="form-horizontal" action="" method="POST">      
         <div class="container-fluid">
            <div class="panel-info">
               <div class="panel-heading text-uppercase">
                    <h4>Alterar Usuário</h4>
               </div>
            </div>
         </div>
         <div class="container-fluid">
            <div class="panel panel-default">
               <div class="panel-body">
                  <!-- deve ficar o código -->
                  <div class="form-group">
                     <label for="nome"  class="col-sm-2 control-label">Nome:</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" name="nome" autofocus="" required="" id="nome" placeholder="João" value="<?=$dados['user']['nome']?>">
                     </div>
                  </div> 
                  <div class="form-group">
                     <label for="sobrenome"  class="col-sm-2 control-label">Sobrenome:</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" id="sobrenome" required="" name="sobrenome" placeholder="Silva" value="<?=$dados['user']['sobrenome']?>">
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="id_orgao_pagador" class="col-sm-2 control-label">Instituição Patrocinadora:</label>
                     <div class="col-sm-5">
                        <select class="form-control" name="id_orgao_pagador" id="id_orgao_pagador">
                           <?php 
                           
                           $id_op = $dados['user']['id_orgao_pagador'];
                           //print_r($id_op);
                           
                           foreach ($dados['OrgaoPagador'] as $fp) {
                               
                                   ?>              
                           <option value="<?= $fp['id_orgao_pagador'];?>"<?=($fp['id_orgao_pagador']==$id_op)?'selected':''?>
                                     name="id_orgao_pagador"><?= $fp['descricao']; ?>
                           </option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="salario" class="col-sm-2 control-label">Remuneração:</label>
                     <div class="col-sm-4">
                        <input type="text" class="form-control" required="" name="salario" id="salario" placeholder="R$.." value="<?=$dados['user']["salario"];?>">
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="email" name="email" class="col-sm-2 control-label">E-mail:</label>
                     <div class="col-sm-4">
                        <input type="email" class="form-control" required="" name="email" id="email" placeholder="nome@gmail.com" value="<?=$dados['autenticacao']['email']?>">
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="senha" name="senha" class="col-sm-2 control-label">Senha:</label>
                     <div class="col-sm-4">
                        <input autocomplete="off" type="password" class="form-control" required="" name="senha" id="senha" placeholder="Digite sua senha...">
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="id_status_usuario" name="senhid_status_usuarioa" class="col-sm-2 control-label">Status:</label>
                     <div class="col-sm-2">
                        <select class="form-control" name="id_status_usuario" id="status_usuario">
                           <?php 
                              foreach ($dados['StatusUsuario'] as $su) { ?>
                           <option value="<?= $su['id_status_usuario']; ?>" 
                              name="id_status_usuario"><?= $su["descricao"] ?>
                           </option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>
                  <div class="col-sm-offset-5 col-sm-10">
                     <a class="btn btn-primary text-uppercase text-center" role="button" href="?action=listaUsuario">Voltar</a>                
                     <input class="btn btn-success text-uppercase text-center" role="button" type="submit" value="Salvar" />
                  </div>
               </div>
            </div>
         </div>
      </form>
   </body>
</html>