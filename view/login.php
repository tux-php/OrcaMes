<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6 lt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7 lt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8 lt8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en-US" class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Sistema de Controle Orçamentário</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <meta name="description" content="Login and Registration Form with HTML5 and CSS3" />
        <meta name="keywords" content="html5, css3, form, switch, animation, :target, pseudo-class" />
        <meta name="author" content="Codrops" />
        <link rel="shortcut icon" href="../favicon.ico"> 
        <link rel="stylesheet" type="text/css" href="./css/login/css/estilo-fernando.css" />
        <link rel="stylesheet" type="text/css" href="./css/login/css/demo.css" />
        <link rel="stylesheet" type="text/css" href="./css/login/css/style.css" />
        <link rel="stylesheet" type="text/css" href="./css/login/css/animate-custom.css" />        
        
    </head>
    <body>        
        <div class="container">
            <!-- Codrops top bar -->
            <!--            <div class="codrops-top">
                            <a href="">
                                <strong>&laquo; Previous Demo: </strong>Responsive Content Navigator
                            </a>
                            <span class="right">
                                <a href=" http://tympanus.net/codrops/2012/03/27/login-and-registration-form-with-html5-and-css3/">
                                    <strong>Back to the Codrops Article</strong>
                                </a>
                            </span>
                            <div class="clr"></div>
                        </div>/ Codrops top bar -->
            <header>
                <h1>Sistema de Controle Orcamentario<span> da Familia</span></h1>

            </header>
            <section>				
                <div id="container_demo" >
                    <!-- hidden anchor to stop jump http://www.css3create.com/Astuce-Empecher-le-scroll-avec-l-utilisation-de-target#wrap4  -->
                    <a class="hiddenanchor" id="toregister"></a>
                    <a class="hiddenanchor" id="tologin"></a>
                    <div id="wrapper">
                        <div id="login" class="animate form">
                            <img src="./images/autismo.png" alt="Símbolo do autismo" />
                            <form  action="" method="post" autocomplete="on">                                 
                                <h1>Log in</h1> 
                                <p> 
                                    <label for="username" class="uname" data-icon="u" > E-mail</label>
                                    <input id="usuario" name="usuario" required="required" type="text" placeholder="email@mail.com"/>
                                </p>
                                <p> 
                                    <label for="password" class="youpasswd" data-icon="p"> Senha </label>
                                    <input id="senha" name="senha" required="required" type="password" placeholder="eg. X8df!90EO" /> 
                                </p>
                                <p class="keeplogin"> 
                                    <input type="checkbox" name="loginkeeping" id="loginkeeping" value="loginkeeping" /> 
                                    <label for="loginkeeping">Matenha-me conectado?</label>
                                </p>
                                <p class="login button"> 
                                    <input type="submit" value="Login" /> 
                                </p>
                                <p class="change_link">
                                    Recuperação de Senha
                                    <a href="#toregister" class="to_register">Clique Aqui</a>
                                </p>
                            </form>
                        </div>

                        <div id="register" class="animate form">
                            <form  action="" method="post" autocomplete="on"> 
                                <h1> Sign up </h1> 
                                <p> 
                                    <label for="usernamesignup" class="uname" data-icon="u">Your username</label>
                                    <input id="usernamesignup" name="usernamesignup" required="required" type="text" placeholder="mysuperusername690" />
                                </p>
                                <p> 
                                    <label for="emailsignup" class="youmail" data-icon="e" > Your email</label>
                                    <input id="emailsignup" name="emailsignup" required="required" type="email" placeholder="mysupermail@mail.com"/> 
                                </p>
                                <p> 
                                    <label for="passwordsignup" class="youpasswd" data-icon="p">Your password </label>
                                    <input id="passwordsignup" name="passwordsignup" required="required" type="password" placeholder="eg. X8df!90EO"/>
                                </p>
                                <p> 
                                    <label for="passwordsignup_confirm" class="youpasswd" data-icon="p">Please confirm your password </label>
                                    <input id="passwordsignup_confirm" name="passwordsignup_confirm" required="required" type="password" placeholder="eg. X8df!90EO"/>
                                </p>
                                <p class="signin button"> 
                                    <input type="submit" value="Sign up"/> 
                                </p>
                                <p class="change_link">  
                                    Already a member ?
                                    <a href="#tologin" class="to_register"> Go and log in </a>
                                </p>
                            </form>
                        </div>

                    </div>
                </div>  
            </section>
        </div>
    </body>
</html>