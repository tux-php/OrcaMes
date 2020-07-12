<!DOCTYPE html>
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
        <h2>Novo Usuário</h2>
        <fieldset>
            <form action="" method="POST">
                <table>
                    <tr>
                        <td>Nome:</td>
                        <td><input type="text" name="nome" id="" /></td>
                    </tr>
                    <tr>
                        <td>Sobrenome:</td>
                        <td><input type="text" name="sobrenome" id="" /></td>
                    </tr>
                    <tr>
                        <td>Instituição Patrocinadora:</td>
                        <td>
                            <select name="id_orgao_pagador" id="">
                                <?php foreach($dados['OrgaoPagador'] as $fp){
                                    
                                    ?>
                                <option value="<?=$fp['id_orgao_pagador']?>" name="id_orgao_pagador"><?=$fp['descricao']?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                        <tr>
                        <td>Salário</td>
                        <td><input type="text " name="salario" id="salario" /></td>
                        </tr>                        
                        <tr>
                            <td>Email:</td>
                            <td><input type="email" name="email" id="" size="40"/></td>
                        </tr>
                        <tr>
                            <td>Senha:</td>
                            <td><input type="password" name="senha" id="" size="40"/></td>
                        </tr>
                    <tr>
                        <tr>
                            <td>Status Usuário:</td>
                            <td>
                                <select name="id_status_usuario" id="">
                                    <?php
                                        foreach($dados['StatusUsuario'] as $su){
                                    ?>
                                    <option value="<?=$su['id_status_usuario'];?>" name="id_status_usuario"><?=$su["descricao"]?></option>
                                        <?php } ?>
                                </select>
                                
                            </td>
                        </tr>
                        <td><input type="submit" value="Salvar" /></td>
                    </tr>
                </table>
                
            </form>
        </fieldset>
        <br /><a href="?action=home">Voltar</a>
    </body>
</html>