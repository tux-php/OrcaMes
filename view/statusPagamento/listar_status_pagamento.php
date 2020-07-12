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
        <h1>Status de Pagamentos</h1>
        <table border="1">
            <tr>
                <th>Id</th>
                <th>Chave</th>
                <th>Descrição</th>                
            </tr>            
            <?php
            if ($dados['statusPagamento']) {
                foreach ($dados['statusPagamento'] as $tp) {
                    ?>
                    <tr>
                        <td><?php echo $tp['id_status_pagamento']; ?></td>
                        <td>
                            <a href="?action=statusPagamento&id=<?php echo $tp['id_status_pagamento']; ?>">
                                <?php echo $tp['descricao']; ?>
                            </a>
                        </td>
                        <td>
                            <a href="?action=alterarSP&id_status_pagamento=<?php echo $tp['id_status_pagamento'] ?>">Alterar</a>
                        </td>
                        <td>
                            <a href="?action=excluirSP&id_status_pagamento=<?php echo $tp['id_status_pagamento'] ?>">Excluir</a>
                        </td>
                    </tr>                                   
                <?php }
            };
            ?>            
        </table>

        <a href="?action=inserirStatuPagamento">Novo Status</a>
        <br /><a href="?action=home">Voltar</a>

    </body>
</html>