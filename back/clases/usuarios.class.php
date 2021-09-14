<?php

require_once "helpers.class.php";
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class usuarios extends conexion{

    
    private $table = "usuarios";
    private $id = "";

    private $nombre = "";
    private $apellidos = "";
    private $password = "";
    private $fecha = "";
    private $email = "";
    private $token = "";
    private $icon_path= "";
    private $estado = "Activo";
    private $role = "user";



    #PARA EL POST-----------  HACER CREATE
    public function post($json){
        $_respuestas = new respuestas;
        $_helpers = new Helpers;
        $datos = json_decode($json, true);
                
        #comprobamos si todos los datos requeridos nos llegaron
        if (!isset($datos['nombre']) || !isset($datos['password']) || !isset($datos['email'])) {
            return $_respuestas->error_400();
        }else{

            $conexion = $this->conexion;
            /* var_dump($conexion);die(); */
            #estos se dejan asi ya que en el if de arriba se confirma su existencia
            $this->nombre = mysqli_real_escape_string($conexion, $datos['nombre']);

            /* encriptado de la contraseña */
            $password = mysqli_real_escape_string($conexion, $datos['password']);
            $password_segura = password_hash($password, PASSWORD_BCRYPT, ['cost' =>4]);
            $this->password = $password_segura;

            
            if(isset($datos['apellidos'])) { $this->apellidos = mysqli_real_escape_string($conexion, $datos['apellidos']); }
            if(isset($datos['fecha'])) { $this->fecha = mysqli_real_escape_string($conexion, $datos['fecha']); }
            

            #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
            $email = mysqli_real_escape_string($conexion, $datos['email']);
            
            $comprobarEmail = $_helpers->validarEmail($email);
            if ($comprobarEmail == 1) {
                return $_respuestas->error_500("Este email ya existe, por favor cambielo");
            }else{
                
                $this->email = $email;
                /* procesamiento de la imagen */
                if (isset($datos['icon_path']) && !empty($datos['icon_path'])) {
                    
                    $icon_path = mysqli_real_escape_string($conexion, $datos['icon_path']);
                    $resp = $_helpers->procesarimage_path($icon_path);
                    $this->icon_path = $resp;
                }

                $resp = $this->insertarUsuario();
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

    }




    private function insertarUsuario(){
        $query = "INSERT INTO " . $this->table ." (nombre, apellidos, email, password, icon_path, fecha, Estado, ROLE) 
        VALUES
        ('" . $this->nombre . "', '" . $this->apellidos . "', '" . $this->email . "', 
        '" . $this->password . "', '" . $this->icon_path . "' , '" . $this->fecha . "', '" . $this->estado . "', '" . $this->role . "') ";
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
        $_respuestas = new respuestas;
        $_helpers = new Helpers;
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

                    $usuarioToken = $_helpers->usuarioToken($this->token);
                    $this->usuario_id = $_helpers->usuario_id($datos['id'], $this->table);
                    
                    if ($usuarioToken != $this->usuario_id) {
                        return $_respuestas->error_401('no tienes permisos para eliminar este usuario');
                    }else{

                        $this->id = $datos["id"];
                        /* var_dump($conexion);die(); */
                        #estos se dejan asi ya que en el if de arriba se confirma su existencia
                        $this->nombre = mysqli_real_escape_string($conexion, $datos['nombre']);

                        /* encriptado de la contraseña */
                        $password = mysqli_real_escape_string($conexion, $datos['password']);
                        $password_segura = password_hash($password, PASSWORD_BCRYPT, ['cost' =>4]);
                        $this->password = $password_segura;

                        

                        if(isset($datos['apellidos'])) { $this->apellidos = mysqli_real_escape_string($conexion, $datos['apellidos']); }
                        if(isset($datos['fecha'])) { $this->fecha = mysqli_real_escape_string($conexion, $datos['fecha']); }

                        $email = mysqli_real_escape_string($conexion, $datos['email']);
                        
                        $comprobarEmail = $_helpers->validarEmail($email);
                        if ($comprobarEmail == 1) {
                            return $_respuestas->error_500("Este email ya existe, por favor cambielo");
                        }else{
                            
                            $this->email = $email;
                            /* procesamiento de la imagen */
                            if (isset($datos['icon_path'])) {
                                
                                $icon_path = mysqli_real_escape_string($conexion, $datos['icon_path']);
                                $resp = $_helpers->procesarimage_path($icon_path);
                                $this->icon_path = $resp;
                            }

                            #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                            $resp = $this->modificarUsuario();
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


    
    private function modificarUsuario(){
        
        $query = "UPDATE " . $this->table ." SET nombre = '" . $this->nombre . "', apellidos =  '" . $this->apellidos . "',
        email = '" . $this->email . "', password = '" . $this->password . "', icon_path = '" . $this->icon_path . "', 
        fecha = '" . $this->fecha . "', Estado = '" . $this->estado . "', ROLE = '" . $this->role . "'  
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
        $_helpers = new Helpers;
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
                    $usuarioToken = $_helpers->usuarioToken($this->token);
                    
                    if ($usuarioToken != $datos['id']) {
                        return $_respuestas->error_401('no tienes permisos para eliminar este usuario');
                    }else{
                        #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                        $this->id = $datos['id'];


                        #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                        $resp = $this->eliminarUsuario();
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


    private function eliminarUsuario(){
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


