<?php

require_once "helpers.class.php";
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class Comentarios extends conexion{

    
    private $table = "comentarios";
    private $id = "";

    private $usuario_id = "";
    private $publicacion_id = "";

    private $contenido = "";

    private $fecha = "";
    
    private $token = "";





    #PARA EL POST-----------  HACER CREATE
    public function post($json){
        $_respuestas = new respuestas;
        $_helpers= new Helpers;
        $datos = json_decode($json, true);

        #para comprobar si enviaron el token
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = $datos['token'];
            $arrayToken = $_helpers->buscarToken($this->token);
            if ($arrayToken) {
                

                
                #comprobamos si todos los datos requeridos nos llegaron
                if (!isset($datos['contenido']) || !isset($datos['publicacion_id']) 
                || !isset($datos['usuario_id']) || !isset($datos['fecha'])) {
                    return $_respuestas->error_400();
                }else{

                    $conexion = $this->conexion;
                    /* var_dump($conexion);die(); */
                    #estos se dejan asi ya que en el if de arriba se confirma su existencia
                    $this->publicacion_id = mysqli_real_escape_string($conexion, $datos['publicacion_id']);
                    $this->usuario_id = mysqli_real_escape_string($conexion, $datos['usuario_id']);
                    $this->contenido = mysqli_real_escape_string($conexion, $datos['contenido']); 
                    $this->fecha = mysqli_real_escape_string($conexion, $datos['fecha']); 
                    

                    #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                    $resp = $this->insertarComentario();
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



    private function insertarComentario(){
        $query = "INSERT INTO " . $this->table ." (usuario_id, publicacion_id, contenido,  fecha) 
        VALUES
        ( '" . $this->usuario_id . "', '" . $this->publicacion_id . "',
        '" . $this->contenido . "', '" . $this->fecha . "') ";
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
        $datos = json_decode($json, true);
        $_helpers= new Helpers;


        #para comprobar si enviaron el token!!!!
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = $datos['token'];
            $arrayToken = $_helpers->buscarToken($this->token);
            if ($arrayToken) {
                
                #comprobamos si todos los datos requeridos nos llegaron
                if (!isset($datos['id'])) {
                    return $_respuestas->error_400('no has enviado el id del comentario a modificar');
                }else{
                    

                
                #comprobamos si todos los datos requeridos nos llegaron
                if (!isset($datos['contenido']) || !isset($datos['publicacion_id']) 
                || !isset($datos['usuario_id']) || !isset($datos['fecha'])) {
                    return $_respuestas->error_400();
                }else{

                    $conexion = $this->conexion;
                    /* var_dump($conexion);die(); */
                    #estos se dejan asi ya que en el if de arriba se confirma su existencia
                    $this->publicacion_id = mysqli_real_escape_string($conexion, $datos['publicacion_id']);
                    $this->usuario_id = mysqli_real_escape_string($conexion, $datos['usuario_id']);
                    $this->contenido = mysqli_real_escape_string($conexion, $datos['contenido']); 
                    $this->fecha = mysqli_real_escape_string($conexion, $datos['fecha']); 
                    
                        #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                        $resp = $this->modificarComentario();
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
                

            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }


    
    private function modificarComentario(){
        
        $query = "UPDATE " . $this->table ."contenido =  '" . $this->contenido . "',
        usuario_id = '" . $this->usuario_id . "', publicacion_id = '" . $this->publicacion_id . "',
        fecha = '" . $this->fecha . "'
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
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);
        $_helpers= new Helpers;



        #para comprobar si enviaron el token
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = $datos['token'];
            $arrayToken = $_helpers->buscarToken($this->token);
            if ($arrayToken) {
                
                #comprobamos si todos los datos requeridos nos llegaron
                if (!isset($datos['id'])) {
                    return $_respuestas->error_400();
                }else{
                    #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                    $this->id = $datos['id'];


                    #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                    $resp = $this->eliminarComentario();
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
            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }


    private function eliminarComentario(){
        $query = "DELETE FROM ". $this->table ." WHERE id = '" . $this->id . "'";
        $resp = parent::nonQuery($query);

        if ($resp >= 1) {
            return $resp; 
        }else{
            return 0;
        }
    }
}





