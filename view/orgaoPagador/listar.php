<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <h1>Orgão Pagador</h1>
        <table border="1">
            <tr>
                <th>Id</th>
                <th>Descrição</th>                
            </tr>            
            <?php            
            if($dados['orgaoPagador']){
                foreach($dados['orgaoPagador'] as $orgao){ ?>
                    <tr>
                        <td><?php echo $orgao['id_orgao_pagador'];?></td>
                        <td>
                            <a href="?action=orgaoPagador&id=<?php echo $orgao['id_orgao_pagador'];?>">
                            <?php echo $orgao['descricao'];?>
                            </a>
                        </td>
                        <td>
                            <a href="?action=editarOP&id_orgao_pagador=<?php echo $orgao['id_orgao_pagador']?>">Alterar</a>
                        </td>
                        <td>
                            <a href="?action=excluirOP&id_orgao_pagador=<?php echo $orgao['id_orgao_pagador']?>">Excluir</a>
                        </td>
                    </tr>                                   
            <?php }};?>            
        </table>

                <a href="?action=inserirOP">Novo Orgão</a>
                <br /><a href="?action=home">Voltar</a>
</body>
</html>