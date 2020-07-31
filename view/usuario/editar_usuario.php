<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />        
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <!--<script type="text/javascript" src="././js/bootstrap.min.js"></script> -->
        <script type="text/javascript" src="././jquery/jquery.maskMoney.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
        <meta charset="utf-8" />
    </head>
    <body>
    
        <form class="form-horizontal" action="" method="POST">
        <div class="container-fluid">
                    <div class="panel-info">
                        <div class="panel-heading text-uppercase"><h4>Alterar Usuário</h4></div>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="nome"  class="col-sm-1 control-label">Nome</label>
                                <div class="col-sm-11">
                                    <input type="text" class="form-control" name="nome" id="nome" placeholder="João" value="<?=$dados['user']['nome']?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="sobrenome"  class="col-sm-1 control-label">Sobrenome:</label>
                                <div class="col-sm-11">
                                    <input type="text" class="form-control" id="sobrenome" name="sobrenome" placeholder="Silva" value="<?=$dados['user']['sobrenome']?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="id_orgao_pagador" class="col-sm-1 control-label">Instituição Patrocinadora:</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="id_orgao_pagador" id="id_orgao_pagador">
                                        <?php foreach ($dados['OrgaoPagador'] as $fp) { ?>                    
                                            <option value="<?= $fp['id_orgao_pagador'] ?>"
                                                name="id_orgao_pagador"><?= $fp['descricao']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="salario" class="col-sm-1 control-label">Remuneração:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="salario" id="salario" placeholder="R$.." value="<?=$dados['user']["salario"];?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" name="email" class="col-sm-1 control-label">E-mail:</label>
                                <div class="col-sm-4">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="nome@gmail.com" value="<?=$dados['autenticacao']['email']?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="senha" name="senha" class="col-sm-1 control-label">Senha:</label>
                                <div class="col-sm-4">
                                    <input type="password" class="form-control" name="senha" id="senha" placeholder="@#$%*()..." value="<?=$dados['autenticacao']['senha']?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="id_status_usuario" name="senhid_status_usuarioa" class="col-sm-1 control-label">Status:</label>
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

                            <div>
                    <a class="btn btn-primary text-uppercase text-center" role="button" href="?action=home">Voltar</a>                
                    <input class="btn btn-success text-uppercase text-center" role="button" type="submit" value="Salvar" />
                </div>      
                        </div>

                        
                    </div>
                </div>    
                
        </form>
        
    </body>
</html>