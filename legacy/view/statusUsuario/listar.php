<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script> 
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <h1>Status Usuário</h1>
        <table border="1">
            <tr>
                <th>Id</th>
                <th>Descrição</th>                
            </tr>            
            <?php            
            if($dados['statusUsuario']){
                foreach($dados['statusUsuario'] as $status){ ?>
                    <tr>
                        <td><?php echo $status['id_status_usuario'];?></td>
                        <td>
                            <a href="?action=statusUsuario&id=<?php echo $status['id_status_usuario'];?>">
                            <?php echo $status['descricao'];?>
                            </a>
                        </td>
                        <td>
                            <a href="?action=alterarStatusUsuario&id_status_usuario=<?php echo $status['id_status_usuario']?>">Alterar</a>
                        </td>
                        <td>
                            <a href="?action=excluirStatusUsuario&id_status_usuario=<?php echo $status['id_status_usuario']?>">Excluir</a>
                        </td>
                    </tr>                                   
            <?php }};?>            
        </table>

                <a href="?action=inserirStatusUsuario">Novo Status Usuário</a>
                <br /><a href="?action=home">Voltar</a>
</body>
</html>