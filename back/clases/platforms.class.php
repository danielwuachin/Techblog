<?php

require_once "conexion/conexion.php";
require_once "respuestas.class.php";
require_once "helpers.class.php";

class Platforms extends conexion{

    
    private $table = "platforms";
    private $id = "";
    private $user_id = "";
    private $platform = "";
    private $token = "";



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
                

                if($admin_verify != 'admin'){
                    return $_respuestas->error_401("area solo para administradores, no tienes permisos suficioentes");
                }else{

                    #comprobamos si todos los datos requeridos nos llegaron
                    if (!isset($datos['platform'])) {
                        return $_respuestas->error_400();
                    }else{
    
                        /* var_dump($conexion);die(); */
                        #estos se dejan asi ya que en el if de arriba se confirma su existencia
                        $this->platform = mysqli_real_escape_string($conexion, $datos['platform']);
                        $this->user_id = $_helpers->userToken($token);
    
                        $resp = $this->insertPlatform();
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



    private function insertPlatform(){
        $query = "INSERT INTO " . $this->table ." (user_id, platform) 
        VALUES
        ('" . $this->user_id . "', '" . $this->platform . "') ";
        $resp = parent::nonQueryId($query);
        /* var_dump($query); */
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
                var_dump($is_admin);
                
                if($admin_verify != 'admin'){
                    return $_respuestas->error_401("area solo para administradores, no tienes permisos suficioentes");
                }else{
                    #comprobamos si todos los datos requeridos nos llegaron
                    if (!isset($datos['id'])) {
                        return $_respuestas->error_400();
                    }else{
                        
                        $userToken = $_helpers->userToken($this->token);
                        $this->user_id = $_helpers->user_id($datos['id'], $this->table);
                        
                        if ($userToken != $this->user_id) {
                            return $_respuestas->error_401('no tienes permisos para modificar esta categoria');
                        }else{
                            
                            $this->id = $datos["id"];
                            
                            $this->platform = mysqli_real_escape_string($conexion, $datos['platform']);
                            
                            #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                            $resp = $this->modifyPlatform();
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

    
    
    private function modifyPlatform(){
        
        $query = "UPDATE " . $this->table ." SET platform = '" . $this->platform . "'
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

                        $userToken = $_helpers->userToken($this->token);
                        $this->user_id = $_helpers->user_id($datos['id'], $this->table);
                        
                        if ($userToken != $this->user_id) {
                            return $_respuestas->error_401('no tienes permisos para delete esta platform');
                        }else{
                            #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                            $this->id = $datos['id'];
        
        
                            #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                            $resp = $this->deletePlatform();
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


    private function deletePlatform(){
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


