<?php
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', 1);
ini_set('error_log', '../log/php_errors.log');

function conection (){
    try{
        $host = "localhost"; 
        $usr = "root";
        $pass = ""; 
        $bd = "color-comida"; 
        $puerto = 3306;
        $mysqli = new mysqli ($host, $usr, $pass, $bd, $puerto); 
        return $mysqli;
    }catch (Exception $e){
        echo "Se ha producido un error en la Conexión:".$e->getMessage();
    }
}

?>
