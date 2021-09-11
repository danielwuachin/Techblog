<?php
require_once "conexion/conexion.php";

class Helpers extends conexion{
    /* hacer pagination */
    public function listar($pagina = 1, $tabla){

        $inicio = 0;
        $cantidad = 100;
        if ($pagina > 1) {
            $inicio = ($cantidad *($pagina - 1 )) +1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id, Nombre, DNI,Telefono,email FROM ". $tabla . " LIMIT $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }


    /* obtener solo uno */
    public function obtener($id, $tabla){
        $query = "SELECT * FROM ". $tabla ." WHERE  id = '$id'";
        return parent::obtenerDatos($query);
    }
    
    /* obtener todo */
    public function obtenerAll($tabla){
        $query = "SELECT * FROM ". $tabla ;
        return parent::obtenerDatos($query);
    }


    /* verificar si es admin */
    public function isAdmin($token, $tabla = "usuarios"){
        $query = "SELECT ROLE FROM $tabla WHERE id = ( SELECT UsuarioId FROM usuarios_token WHERE Token = '". $token ."' ) ";
        $resultado = parent::obtenerDatos($query);
        var_dump($resultado);
        return $resultado;
    }




    public function buscarToken($token){
        $query = "SELECT  tokenId, UsuarioId, Estado FROM usuarios_token WHERE Token = '" . $token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);

        if ($resp) {
            return $resp;
        }else{
            return 0;
        }
    }

    #para actualizar el token cada vez que se realize una consulta
    public function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuarios_token SET Fecha = '$date' WHERE tokenId = '$tokenid'";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        }else{
            return 0;
        }
    }
}
