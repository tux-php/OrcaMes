<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <!--<link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />-->
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>        
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <div class="container-fluid">
        <div class="panel-info">
        <div class="panel-heading text-uppercase"><strong>Lista de Usuários</strong></div>
    </div>
    <div class="table-responsive">
    <table class="table table-hover table-striped table-condensed">
        <thead>
            <tr>
                <th scope="col" class="text-uppercase text-left">#</th>    
                <th scope="col" class="text-uppercase text-left">Nome</th>
                <th scope="col" class="text-uppercase text-left">Instituição Patrocinadora</th>
                <th scope="col" class="text-uppercase text-left">Salário</th>
                <th scope="col" class="text-uppercase text-center" colspan="2">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($dados['user'] as $ch=>$usuario){ ?>
            <tr>
                <th scope="row"><?=$ch;?></th>
                <td class="text-uppercase"><?=$usuario["nome"];?></td>
                <td>
                    <?php
                     echo $usuario["id_orgao_pagador"];
                    ?>
                </td>
                <td><?=$usuario["salario"];?></td>                
                <td class="text-center">                    
                    <a class="btn-sm btn-primary text-uppercase text-center" role="button" href="?action=alterarUsuario&id_usuario=<?=$usuario['id_usuario'];?>">Alterar</a>
                    <a class="btn-sm btn-danger text-uppercase text-center" role="button"  href="?action=excluirUsuario&id_usuario=<?=$usuario['id_usuario']?>">Excluir</a>
                </td>                
            </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
    <div class="col-sm-offset-5 col-sm-10">
        <a class="btn btn-primary text-uppercase text-center" role="button" href="?action=home">VOLTAR</a>
        <a class="btn btn-success text-uppercase text-center" role="button" href="?action=inserirUsuario">Novo</a>
    </div>
</body>
</html>