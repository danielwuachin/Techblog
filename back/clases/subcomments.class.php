<?php

require_once "helpers.class.php";
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class Subcomments extends conexion{

    
    private $table = "subcomments";
    private $id = "";

    private $user_id = "";
    private $comment_id = "";

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
                if (!isset($datos['content']) || !isset($datos['comment_id']) 
                || !isset($datos['date'])) {
                    return $_respuestas->error_400();
                }else{

                    /* var_dump($conexion);die(); */
                    #estos se dejan asi ya que en el if de arriba se confirma su existencia
                    $this->user_id = $_helpers->userToken($this->token);
                    $this->comment_id = mysqli_real_escape_string($conexion, $datos['comment_id']);
                    $this->content = mysqli_real_escape_string($conexion, $datos['content']); 
                    $this->date = mysqli_real_escape_string($conexion, $datos['date']); 
                    

                    #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                    $resp = $this->insertSubcomment();
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



    private function insertSubcomment(){
        $query = "INSERT INTO " . $this->table ." (user_id, comment_id, content,  date) 
        VALUES
        ( '" . $this->user_id . "', '" . $this->comment_id . "',
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
                    return $_respuestas->error_400('no has enviado el id del subcomment a modificar');
                }else{
                    

                
                    #comprobamos si todos los datos requeridos nos llegaron
                    if (!isset($datos['content']) || !isset($datos['date'])) {
                        return $_respuestas->error_400();
                    }else{

                        $userToken = $_helpers->userToken($this->token);
                        $this->user_id = $_helpers->user_id($datos['id'], $this->table);
                        
                        if ($userToken != $this->user_id) {
                            return $_respuestas->error_401('no tienes permisos para modificar este comentario');
                        }else{
                        
                            $this->id = mysqli_real_escape_string($conexion, $datos["id"]);
                            /* var_dump($conexion);die(); */
                            #estos se dejan asi ya que en el if de arriba se confirma su existencia
                            $this->content = mysqli_real_escape_string($conexion, $datos['content']); 
                            $this->date = mysqli_real_escape_string($conexion, $datos['date']); 
                            
                            #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                            $resp = $this->modifySubcomment();
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


    
    private function modifySubcomment(){
        
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
                        return $_respuestas->error_401('no tienes permisos para modificar este comentario');
                    }else{
                        #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                        $this->id = $datos['id'];


                        #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                        $resp = $this->deleteSubcomment();
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


    private function deleteSubcomment(){
        $query = "DELETE FROM ". $this->table ." WHERE id = '" . $this->id . "'";
        $resp = parent::nonQuery($query);

        if ($resp >= 1) {
            return $resp; 
        }else{
            return 0;
        }
    }
}





