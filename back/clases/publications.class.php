<?php

require_once "helpers.class.php";
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class Publications extends conexion{

    
    private $table = "publications";
    private $id = "";

    private $title = "";
    private $subtitle = "";
    private $description = "";
    
    private $user_id = "";
    private $category_id = "0";
    private $platform_id = "0";
    
    private $image_path= "";
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
                
                 /* COMPROBAMO SI ES ADMIN */
                $token = $this->token;
                $is_admin = $_helpers->isAdmin($token);
                $admin_verify = $is_admin[0]['ROLE'];
                var_dump($is_admin);

                if($admin_verify == 'admin' || $admin_verify == 'publicador'){
                    #comprobamos si todos los datos requeridos nos llegaron
                    if (!isset($datos['title']) || !isset($datos['subtitle']) || !isset($datos['description']) || !isset($datos['image_path']) ) {
                        return $_respuestas->error_400();
                    }else{

                        /* var_dump($conexion);die(); */
                        #estos se dejan asi ya que en el if de arriba se confirma su existencia
                        $this->title = mysqli_real_escape_string($conexion, $datos['title']);
                        $this->subtitle = mysqli_real_escape_string($conexion, $datos['subtitle']);
                        $this->description = mysqli_real_escape_string($conexion, $datos['description']); 
                        $this->user_id = $_helpers->userToken($this->token);

                        
                        if(isset($datos['date'])) { $this->date = mysqli_real_escape_string($conexion, $datos['date']); }
                        if(isset($datos['category_id'])) { $this->category_id = mysqli_real_escape_string($conexion, $datos['category_id']); }
                        if(isset($datos['platform_id'])) { $this->platform_id = mysqli_real_escape_string($conexion, $datos['platform_id']); }

                        
                        
                        /* procesamiento de la imagen */
                        if (!empty($datos['image_path'])  ) {
                            echo "si esta entrando"; 
                            $image_path = mysqli_real_escape_string($conexion, $datos['image_path']);
                            $resp = $_helpers->procesarimage_path($image_path);
                            $this->image_path = $resp;
                        }

                        #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                        $resp = $this->insertPublication();
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
                    
                    return $_respuestas->error_401("area solo para administradores, no tienes permisos suficioentes");
                }


            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }






    private function insertPublication(){
        $query = "INSERT INTO " . $this->table ." (user_id, category_id, platform_id, image_path, title, subtitle, description,  date) 
        VALUES
        ( '" . $this->user_id . "', '" . $this->category_id . "', '" . $this->platform_id . "' , '" . $this->image_path . "' , 
        '" . $this->title . "', '" . $this->subtitle . "', '" . $this->description . "', '" . $this->date . "') ";
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

                /* COMPROBAMO SI ES ADMIN */
                $token = $this->token;
                $is_admin = $_helpers->isAdmin($token);
                $admin_verify = $is_admin[0]['ROLE'];
                var_dump($is_admin);

                if($admin_verify != 'admin' || $admin_verify != 'publicador'){
                    #comprobamos si todos los datos requeridos nos llegaron
                    if (!isset($datos['id'])) {
                        return $_respuestas->error_400();
                    }else{

                        $userToken = $_helpers->userToken($this->token);
                        $this->user_id = $_helpers->user_id($datos['id'], $this->table);
                        
                        if ($userToken != $this->user_id) {
                            return $_respuestas->error_401('no tienes permisos para modify esta publication');
                        }else{
                        
                            $this->id = mysqli_real_escape_string($conexion, $datos["id"]);
                            /* var_dump($conexion);die(); */
                            #estos se dejan asi ya que en el if de arriba se confirma su existencia
                            if(isset($datos['category_id'])) { $this->category_id = mysqli_real_escape_string($conexion, $datos['category_id']); }
                            if(isset($datos['platform_id'])) { $this->platform_id = mysqli_real_escape_string($conexion, $datos['platform_id']); }
                            
                            
                            if(isset($datos['title'])) { $this->title = mysqli_real_escape_string($conexion, $datos['title']); }
                            if(isset($datos['subtitle'])) { $this->subtitle = mysqli_real_escape_string($conexion, $datos['subtitle']); }
                            if(isset($datos['description'])) { $this->description = mysqli_real_escape_string($conexion, $datos['description']); }
                            if(isset($datos['date'])) { $this->date = mysqli_real_escape_string($conexion, $datos['date']); }

                            
                            
                            /* procesamiento de la imagen */
                            if (isset($datos['image_path']) && !empty($datos['image_path'])  ) {
                                $image_path = mysqli_real_escape_string($conexion, $datos['image_path']);
                                $resp = $_helpers->procesarimage_path($image_path);
                                $this->image_path = $resp;
                            }

                            #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                            $resp = $this->modifyPublication();
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
                    return $_respuestas->error_401("area solo para administradores, no tienes permisos suficioentes");
                    
                }
                    

            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }


    
    private function modifyPublication(){
        
        $query = "UPDATE " . $this->table ." SET title = '" . $this->title . "', subtitle = '" . $this->subtitle . "', description =  '" . $this->description . "',
        category_id = '" . $this->category_id . "', image_path = '" . $this->image_path . "', 
        date = '" . $this->date . "', platform_id = '" . $this-> platform_id . "'  
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
                
                /* COMPROBAMO SI ES ADMIN */
                $token = $this->token;
                $is_admin = $_helpers->isAdmin($token);
                $admin_verify = $is_admin[0]['ROLE'];
                var_dump($is_admin);

                if($admin_verify != 'admin' || $admin_verify != 'publicador'){
                    
                                        #comprobamos si todos los datos requeridos nos llegaron
                                        if (!isset($datos['id'])) {
                                            return $_respuestas->error_400();
                                        }else{
                    
                                            $userToken = $_helpers->userToken($this->token);
                                            $this->user_id = $_helpers->user_id($datos['id'], $this->table);
                                            
                                            if ($userToken != $this->user_id) {
                                                return $_respuestas->error_401('no tienes permisos para delete esta publication');
                                            }else{
                                                #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                                                $this->id = $datos['id'];
                    
                    
                                                #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                                                $resp = $this->deletePublication();
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
                    return $_respuestas->error_401("area solo para administradores, no tienes permisos suficioentes");
                }
            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }


    private function deletePublication(){
        $query = "DELETE FROM ". $this->table ." WHERE id = '" . $this->id . "'";
        $resp = parent::nonQuery($query);

        if ($resp >= 1) {
            return $resp; 
        }else{
            return 0;
        }
    }
}





	