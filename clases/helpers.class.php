<?php
require_once "conexion/conexion.php";

class Helpers extends conexion{
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



    public function obtener($id, $tabla){
        $query = "SELECT * FROM ". $tabla ." WHERE  id = '$id'";
        return parent::obtenerDatos($query);
    }
    
    public function obtenerAll($tabla){
        $query = "SELECT * FROM ". $tabla ;
        return parent::obtenerDatos($query);
    }


    /* verificar si es admin */
    public function isAdmin($postid, $tabla = "usuarios"){
        $query = "SELECT 'ROLE' FROM ". $tabla . "WHERE id = '$postid'";
        $resultado = parent::obtenerDatos($query);
        var_dump($resultado);
    }
}