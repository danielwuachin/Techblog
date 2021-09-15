<?php 
require_once 'conexion/conexion.php';
class token extends conexion {
    public function actualizarToken($fecha){
        $query = "UPDATE usuarios_token SET Estado = 'InActive' WHERE Fecha < '$fecha' AND Estado = 'Active'" ;
        $verificar = parent::nonQuery($query);

        if ($verificar > 0) {
            return 1;
        }else{
            return 0;
        }
    }
}