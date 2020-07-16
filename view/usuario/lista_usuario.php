<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <link rel="stylesheet" href="././css/meu_estilo/estilo_meu.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>        
        <script type="text/javascript" src="././js/bootstrap.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <h1>Lista Usuários</h1>
        <table border="1">
            <tr>
                <td>Nome</td>
                <td>Órgão Pagador</td>
                <td>Salário</td>                
                <td><a href="">Alterar</a></td>
                <td><a href="">Remover</a></td>
            </tr>
            <?php
            //print_r($dados['user']);die();
            foreach($dados['user'] as $usuario){
                    
                ?>
            <tr>
                <td><?=$usuario["nome"];?></td>
                <td>
                    <?php
                   
                   echo $usuario["id_orgao_pagador"];
                    //var_dump($dados['user'][$i]['id_orgao_pagador']);die();
                    
                    ?>
                </td>
                <td><?=$usuario["salario"];?></td>                
                <td><a href="?action=alterarUsuario&id_usuario=<?=$usuario['id_usuario'];?>">Alterar</a></td>
                <td><a href="?action=excluirUsuario&id_usuario=<?=$usuario['id_usuario']?>">Excluir</a></td>
            </tr>
            <?php } ?>
        </table>
        <a href="?action=inserirUsuario">Cadastrar</a><br />
        <a href="?action=home">VOLTAR</a>
        
</body>
</html>