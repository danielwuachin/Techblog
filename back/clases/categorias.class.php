<?php

require_once "conexion/conexion.php";
require_once "respuestas.class.php";
require_once "helpers.class.php";

class Categorias extends conexion{

    
    private $table = "categorias";
    private $id = "";
    private $genero = "";
    private $token = "";
    private $usuario_id = "";



    #PARA EL POST-----------  HACER CREATE
    public function post($json){
        $_helpers = new Helpers;
        $_respuestas = new respuestas;
        $conexion = $this->conexion;
        $datos = json_decode($json, true);


        #para comprobar si enviaron el token
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = mysqli_real_escape_string($conexion, $datos['token']);
            $arrayToken = $_helpers->buscarToken($this->token);
            if ($arrayToken) {


                /* COMPROBAMO SI ES ADMIN */
                $token = $this->token;
                $is_admin = $_helpers->isAdmin($token);
                $admin_verify = $is_admin[0]['ROLE'];
                var_dump($is_admin);

                if($admin_verify != 'admin'){
                    return $_respuestas->error_401("area solo para administradores, no tienes permisos suficioentes");
                }else{

                    #comprobamos si todos los datos requeridos nos llegaron
                    if (!isset($datos['genero'])) {
                        return $_respuestas->error_400();
                    }else{
    
                        /* var_dump($conexion);die(); */
                        #estos se dejan asi ya que en el if de arriba se confirma su existencia
                        $this->genero = mysqli_real_escape_string($conexion, $datos['genero']);
                        $this->usuario_id = $_helpers->usuarioToken($token);

                        var_dump($this->usuario_id);

                        $resp = $this->insertarCategoria();
                        /* var_dump($resp); */
                        if ($resp) {
                            $respuesta = $_respuestas->response;
                            $respuesta['result'] = array (
                                "id" => $resp
                            );
                            return $respuesta;
                        }else{
                            return $_respuestas->error_500();
                        }
                    }
                }

            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }



    private function insertarCategoria(){
        $query = "INSERT INTO " . $this->table ." (usuario_id, genero) 
        VALUES
        ( '" .$this->usuario_id ."', '" . $this->genero . "' ) ";
        $resp = parent::nonQueryId($query);
        var_dump($query);
        if ($resp) {
            return $resp;
        }else{
            return 0;
        }
    }



    #PARA HACER UPDATE-------- ----------------------------METODO PUT
    public function put($json){
        $_helpers = new Helpers;
        $_respuestas = new respuestas;
        $conexion = $this->conexion;
        $datos = json_decode($json, true);



        #para comprobar si enviaron el token!!!!
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = mysqli_real_escape_string($conexion, $datos['token']);
            $arrayToken = $_helpers->buscarToken($this->token);
            if ($arrayToken) {


                /* COMPROBAMO SI ES ADMIN */
                $token = $this->token;
                $is_admin = $_helpers->isAdmin($token);
                $admin_verify = $is_admin[0]['ROLE'];
                

                if($admin_verify != 'admin'){
                    return $_respuestas->error_401("area solo para administradores, no tienes permisos suficioentes");
                }else{
                    #comprobamos si todos los datos requeridos nos llegaron
                    if (!isset($datos['id'])) {
                        return $_respuestas->error_400();
                    }else{
                        

                        $usuarioToken = $_helpers->usuarioToken($token);
                        $this->usuario_id = $_helpers->usuario_id($datos['id'], $this->table);
                        
                        if ($usuarioToken != $this->usuario_id) {
                            return $_respuestas->error_401('no tienes permisos para modificar esta categoria');
                        }else{
                            
                            $this->id = $datos["id"];
        
                            $this->genero = mysqli_real_escape_string($conexion, $datos['genero']);
        
                            #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                            $resp = $this->modificarCategoria();
                            var_dump($resp);
                            if ($resp) {
                                $respuesta = $_respuestas->response;
                                $respuesta['result'] = array (
                                    "id" => $resp
                                );
                                return $respuesta;
                            }else{
                                return $_respuestas->error_500();
                            }
                        }

                    }
                }
            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }


    
    private function modificarCategoria(){
        
        $query = "UPDATE " . $this->table ." SET genero = '" . $this->genero . "'
        WHERE id = '" . $this->id . "'";
        
        $resp = parent::nonQuery($query);
        
        #COMO NONQUERY DEVUELVE LAS FILAS AFECTADAS, SI ES IGUAL O MAYOR A UNO ES QEU SI FUNCIONO
        if ($resp >= 1) {
            return $resp;
        }else{
            return 0;
        }
    }



    #PARA BORRARR    --------------------------------------------------------------
    public function delete($json){
        $_helpers = new Helpers;
        $_respuestas = new respuestas;
        $conexion = $this->conexion;
        $datos = json_decode($json, true);

        #para comprobar si enviaron el token
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = mysqli_real_escape_string($conexion, $datos['token']);
            $arrayToken = $_helpers->buscarToken($this->token);
            if ($arrayToken) {
                

                /* COMPROBAMO SI ES ADMIN */
                $token = $this->token;
                $is_admin = $_helpers->isAdmin($token);
                $admin_verify = $is_admin[0]['ROLE'];
                var_dump($is_admin);

                if($admin_verify != 'admin'){
                    return $_respuestas->error_401("area solo para administradores, no tienes permisos suficioentes");
                }else{
                    #comprobamos si todos los datos requeridos nos llegaron
                    if (!isset($datos['id'])) {
                        return $_respuestas->error_400();
                    }else{

                        $usuarioToken = $_helpers->usuarioToken($token);
                        $this->usuario_id = $_helpers->usuario_id($datos['id'], $this->table);
                        
                        if ($usuarioToken != $this->usuario_id) {
                            return $_respuestas->error_401('no tienes permisos para eliminar esta categoria');
                        }else{
                            #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                            $this->id = $datos['id'];
        
                            /* $usuario = $_helpers->comprobarUsuario($this->table, $token);
                            $usuarioComprobado = $usuario[0]['id'];
                            var_dump($usuario);die(); */
                            #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                            $resp = $this->eliminarCategoria();
                            if ($resp) {
                                $respuesta = $_respuestas->response;
                                $respuesta['result'] = array (
                                    "id" => $this->id
                                );
                                return $respuesta;
                            }else{
                                return $_respuestas->error_500();
                            }
                        }
                    } 


                }
            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }


    private function eliminarCategoria(){
        $query = "DELETE FROM ". $this->table ." WHERE id = '" . $this->id . "'";
        $resp = parent::nonQuery($query);

        if ($resp >= 1) {
            return $resp; 
        }else{
            return 0;
        }
    }
}
?>


