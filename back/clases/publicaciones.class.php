<?php

require_once "helpers.class.php";
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class Publicaciones extends conexion{

    
    private $table = "publicaciones";
    private $id = "";

    private $titulo = "";
    private $descripcion = "";
    
    private $usuario_id = "";
    private $categoria_id = "0";
    private $plataforma_id = "0";
    
    private $image_path= "";
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
                if (!isset($datos['titulo']) || !isset($datos['descripcion']) || !isset($datos['image_path']) ) {
                    return $_respuestas->error_400();
                }else{

                    $conexion = $this->conexion;
                    /* var_dump($conexion);die(); */
                    #estos se dejan asi ya que en el if de arriba se confirma su existencia
                    $this->titulo = mysqli_real_escape_string($conexion, $datos['titulo']);
                    $this->descripcion = mysqli_real_escape_string($conexion, $datos['descripcion']); 
                    $this->usuario_id = $_helpers->usuarioToken($this->token);

                    
                    if(isset($datos['fecha'])) { $this->fecha = mysqli_real_escape_string($conexion, $datos['fecha']); }
                    if(isset($datos['categoria_id'])) { $this->categoria_id = mysqli_real_escape_string($conexion, $datos['categoria_id']); }
                    if(isset($datos['plataforma_id'])) { $this->plataforma_id = mysqli_real_escape_string($conexion, $datos['plataforma_id']); }

                    
                    
                    /* procesamiento de la imagen */
                    if (!empty($datos['image_path'])  ) {
                        echo "si esta entrando"; 
                        $image_path = mysqli_real_escape_string($conexion, $datos['image_path']);
                        $resp = $_helpers->procesarimage_path($image_path);
                        $this->image_path = $resp;
                    }

                    #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                    $resp = $this->insertarPublicacion();
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






    private function insertarPublicacion(){
        $query = "INSERT INTO " . $this->table ." (usuario_id, categoria_id, plataforma_id, image_path, titulo, descripcion,  fecha) 
        VALUES
        ( '" . $this->usuario_id . "', '" . $this->categoria_id . "', '" . $this->plataforma_id . "' , '" . $this->image_path . "' , 
        '" . $this->titulo . "', '" . $this->descripcion . "', '" . $this->fecha . "') ";
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
                    return $_respuestas->error_400();
                }else{

                    $usuarioToken = $_helpers->usuarioToken($this->token);
                    $this->usuario_id = $_helpers->usuario_id($datos['id'], $this->table);
                    
                    if ($usuarioToken != $this->usuario_id) {
                        return $_respuestas->error_401('no tienes permisos para modificar esta publicacion');
                    }else{
                    
                        $conexion = $this->conexion;
                        $this->id = mysqli_real_escape_string($conexion, $datos["id"]);
                        /* var_dump($conexion);die(); */
                        #estos se dejan asi ya que en el if de arriba se confirma su existencia
                        if(isset($datos['categoria_id'])) { $this->categoria_id = mysqli_real_escape_string($conexion, $datos['categoria_id']); }
                        if(isset($datos['plataforma_id'])) { $this->plataforma_id = mysqli_real_escape_string($conexion, $datos['plataforma_id']); }
                        
                        
                        if(isset($datos['titulo'])) { $this->titulo = mysqli_real_escape_string($conexion, $datos['titulo']); }
                        if(isset($datos['descripcion'])) { $this->descripcion = mysqli_real_escape_string($conexion, $datos['descripcion']); }
                        if(isset($datos['fecha'])) { $this->fecha = mysqli_real_escape_string($conexion, $datos['fecha']); }

                        
                        
                        /* procesamiento de la imagen */
                        if (isset($datos['image_path']) && !empty($datos['image_path'])  ) {
                            $image_path = mysqli_real_escape_string($conexion, $datos['image_path']);
                            $resp = $_helpers->procesarimage_path($image_path);
                            $this->image_path = $resp;
                        }

                            #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                            $resp = $this->modificarPublicacion();
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


    
    private function modificarPublicacion(){
        
        $query = "UPDATE " . $this->table ." SET titulo = '" . $this->titulo . "', descripcion =  '" . $this->descripcion . "',
        categoria_id = '" . $this->categoria_id . "', image_path = '" . $this->image_path . "', 
        fecha = '" . $this->fecha . "', plataforma_id = '" . $this-> plataforma_id . "'  
        WHERE id = '" . $this->id . "'";

        var_dump($query);
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

                    $usuarioToken = $_helpers->usuarioToken($this->token);
                    $this->usuario_id = $_helpers->usuario_id($datos['id'], $this->table);
                    
                    if ($usuarioToken != $this->usuario_id) {
                        return $_respuestas->error_401('no tienes permisos para eliminar esta publicacion');
                    }else{
                        #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                        $this->id = $datos['id'];


                        #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                        $resp = $this->eliminarPublicacion();
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


    private function eliminarPublicacion(){
        $query = "DELETE FROM ". $this->table ." WHERE id = '" . $this->id . "'";
        $resp = parent::nonQuery($query);

        if ($resp >= 1) {
            return $resp; 
        }else{
            return 0;
        }
    }
}





