<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="././css/bootstrap.min.css" />
        <script type="text/javascript" src="././jquery/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="././jquery/jquery.maskMoney.min.js"></script>
        <script type="text/javascript" src="././js/efeitos.js"></script>
        <script type="text/javascript" src="././js/bootstrap.min.js"></script> 
        <meta charset="utf-8" />
    </head>
    
    <body>
        <h2>ALTERAR USUÁRIO</h2>
        <fieldset>
            <form action="" method="POST">
                <table>
                    <tr>
                        <td>Nome:</td>                        
                        <td><input type="text" name="nome" id="" value="<?=$dados['user']['nome']?>" /></td>
                    </tr>
                    <tr>
                        <td>Sobrenome:</td>
                        <td><input type="text" name="sobrenome" id="" value="<?=$dados['user']['sobrenome']?>"/></td>
                    </tr>
                    <tr>
                        <td>Instituição Patrocinadora:</td>
                        
                        <td>
                            <select name="id_orgao_pagador" id="">
                                <?php
                                    
                                    //var_dump($id_op);die();
                                    foreach($dados['OrgaoPagador'] as $op){
                                ?>
                                <option value="<?=$op['id_orgao_pagador']?>" name="id_orgao_pagador">
                                    <?php 
                                    //var_dump($dados['op']);die(); 
                                    //var_dump($op['id_orgao_pagador']);die();                                        
                                            echo $op['descricao'];
                                            
                                        
                                        ?></option>
                                <?php }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Salário:</td>
                        <td>
                            <input type="text" name="salario" id="salario" value="<?=$dados['user']["salario"];?>" />
                        </td>
                    </tr>
                    <tr>
                            <td>Email:</td>
                            <td><input type="text" name="email" id="" size="40" value="<?=$dados['user']['email']?>"/></td>
                        </tr>
                        <tr>
                            <td>Senha:</td>
                            <td><input type="password" name="senha" id="" size="40" value="<?=$dados['user']['senha']?>"/></td>
                        </tr>
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
                    <tr>
                        <td><input type="submit" value="Salvar" /></td>
                    </tr>
                </table>
                
            </form>
        </fieldset>
        <br /><a href="index.php">Voltar</a>
    </body>
</html>