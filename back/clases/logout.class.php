<?php

require_once "helpers.class.php";
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class Logout extends conexion{
    
    private $token = "";


    #PARA EL POST-----------  eliminar token
    public function goodbye($json){
        $_respuestas = new respuestas;
        $_helpers= new Helpers;
        $conexion = $this->conexion;
        $datos = json_decode($json, true);

        #para comprobar si enviaron el token
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = mysqli_real_escape_string($conexion, $datos['token']);
            $arrayToken = $_helpers->buscarToken($this->token);
            if ($arrayToken) {
                $logout = $_helpers->eliminarToken($this->token);
                if ($logout) {
                    $despedida = "Gracias por visitarnos";
                    return $despedida;
                }else{
                    return $_respuestas->error_401('error al eliminar token');
                }
            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }

}