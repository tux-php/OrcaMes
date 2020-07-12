<?php
var_dump($_SESSION);
session_start();
session_destroy();

header("Location:teste.php");
?>