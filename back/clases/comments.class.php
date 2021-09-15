<?php

require_once "helpers.class.php";
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class Comments extends conexion{

    
    private $table = "comments";
    private $id = "";

    private $user_id = "";
    private $publication_id = "";

    private $content = "";

    private $date = "";
    
    private $token = "";





    #PARA EL POST-----------  HACER CREATE
    public function post($json){
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
                
                #comprobamos si todos los datos requeridos nos llegaron
                if (!isset($datos['content']) || !isset($datos['publication_id']) 
                || !isset($datos['date'])) {
                    return $_respuestas->error_400();
                }else{

                    /* var_dump($conexion);die(); */
                    #estos se dejan asi ya que en el if de arriba se confirma su existencia
                    $this->user_id = $_helpers->userToken($this->token);
                    $this->publication_id = mysqli_real_escape_string($conexion, $datos['publication_id']);
                    $this->content = mysqli_real_escape_string($conexion, $datos['content']); 
                    $this->date = mysqli_real_escape_string($conexion, $datos['date']); 
                    

                    #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                    $resp = $this->isnertComment();
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


            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }



    private function isnertComment(){
        $query = "INSERT INTO " . $this->table ." (user_id, publication_id, content,  date) 
        VALUES
        ( '" . $this->user_id . "', '" . $this->publication_id . "',
        '" . $this->content . "', '" . $this->date . "') ";
        $resp = parent::nonQueryId($query);
        var_dump($query);
        var_dump($resp);
        if ($resp) {
            return $resp;
        }else{
            return 0;
        }
    }






    #PARA HACER UPDATE-------- ----------------------------METODO PUT
    public function put($json){
        $_respuestas = new respuestas;
        $_helpers= new Helpers;
        $conexion = $this->conexion;
        $datos = json_decode($json, true);
        

        #para comprobar si enviaron el token!!!!
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = mysqli_real_escape_string($conexion, $datos['token']);
            $arrayToken = $_helpers->buscarToken($this->token);
            if ($arrayToken) {
                
                #comprobamos si todos los datos requeridos nos llegaron
                if (!isset($datos['id'])) {
                    return $_respuestas->error_400('no has enviado el id del comment a modify');
                }else{
                    
                    $userToken = $_helpers->userToken($this->token);
                    $this->user_id = $_helpers->user_id($datos['id'], $this->table);
                    
                    if ($userToken != $this->user_id) {
                        return $_respuestas->error_401('no tienes permisos para modify este comment');
                    }else{
                        
                        #comprobamos si todos los datos requeridos nos llegaron
                        if (!isset($datos['content']) || !isset($datos['date'])) {
                            return $_respuestas->error_400();
                        }else{
                            
                            $this->id = mysqli_real_escape_string($conexion, $datos["id"]);
                            /* var_dump($conexion);die(); */
                            #estos se dejan asi ya que en el if de arriba se confirma su existencia
                            $this->content = mysqli_real_escape_string($conexion, $datos['content']); 
                            $this->date = mysqli_real_escape_string($conexion, $datos['date']); 
                            
                            #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                            $resp = $this->modifyComment();
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
    
    
    
    private function modifyComment(){
        
        $query = "UPDATE " . $this->table ." SET content =  '" . $this->content . "',
        date = '" . $this->date . "'
        WHERE id = '" . $this->id . "'";
        
        
        $resp = parent::nonQuery($query);
        var_dump($query);
        #COMO NONQUERY DEVUELVE LAS FILAS AFECTADAS, SI ES IGUAL O MAYOR A UNO ES QEU SI FUNCIONO
        if ($resp >= 1) {
            return $resp;
        }else{
            return 0;
        }
    }
    
    
    
    
    
    #PARA BORRARR    --------------------------------------------------------------
    public function delete($json){
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
                
                #comprobamos si todos los datos requeridos nos llegaron
                if (!isset($datos['id'])) {
                    return $_respuestas->error_400();
                }else{
                    $userToken = $_helpers->userToken($this->token);
                    $this->user_id = $_helpers->user_id($datos['id'], $this->table);
                    
                    if ($userToken != $this->user_id) {
                        return $_respuestas->error_401('no tienes permisos para delete este comment');
                    }else{
                        #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                        $this->id = $datos['id'];


                        #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                        $resp = $this->deleteComment();
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
            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }


    private function deleteComment(){
        $query = "DELETE FROM ". $this->table ." WHERE id = '" . $this->id . "'";
        $resp = parent::nonQuery($query);

        if ($resp >= 1) {
            return $resp; 
        }else{
            return 0;
        }
    }
}





