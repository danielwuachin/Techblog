<?php 
require_once '../clases/token.class.php';
#PARA QUE ESTO FUNCIONE DEBES IR A LOS CRON JOBS DE TU HOSTING Y PONER CADA CUANTO SE VAN A INHABILITAR
$_token = new token;
#el campo fecha en la DDBB debe ser de tipo fecha
$fecha = date('Y-m-d H:i');
echo $_token->actualizarToken($fecha);



