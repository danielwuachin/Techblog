<?php

require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class auth extends conexion{

    #json recibido de la pagina auth.php
    public function login($json){
        $_respuestas = new respuestas;
        #convertirn  json a un array
        $conexion = $this->conexion;

        $datos = json_decode($json, true);

        #verificar que quien consume la api tiene un campo user y otro pass correctamente
        if (!isset($datos['email']) || !isset($datos['password'])) {
            #error por no tener los campos
            return $_respuestas->error_400();

        }else{
            // todo bien todo correcto
            $email =  mysqli_real_escape_string($conexion, $datos['email']);
            $password =  mysqli_real_escape_string($conexion, $datos['password']);
            
            /* se sacan los datos del email recibido con el de la BD y se comprueba la password */
            $datos = $this->obtenerDatosEmail($email);
            var_dump($datos); 

            if ($datos[0]['status'] != 'Active'){
                return $_respuestas->error_401('por motivos internos, usted esta baneado de esta pagina web');
            }

                /* se envia la pass recibida mas la extraida de la DB */
                $password = parent::encriptar($password, $datos[0]['password']);
                var_dump($password);
                #ver si hay datos
                if ($datos) {
                    # verificar si la contraseña ingresada es igual a la de la DDBB
                    if ($password == $datos[0]['password']) {
                        
                        if ($datos[0]['status'] == 'Active') {
                            #crear el token para el id del email que intenta loguearse
                            $verificar = $this->insertarToken($datos[0]['id']);
                            if ($verificar) {
                                #si se pudo guardar
                                $result = $_respuestas->response;
                                $result["result"] = array(
                                    "token" => $verificar
                                );
                                return $result;
                            }else{
                                #error al guardar
                                return $_respuestas->error_500("error interno, no hemos podido guardar"); 
                            }
                        }else{
                            #error, email no esta Active
                            return $_respuestas->error_401("el email no esta Active mi pana");
                        }
                    }else{
                        return $_respuestas->error_401("el password es invalido");
                    }
                    
                }else{
                    #no eciste el email, se usa error 200 ya que si funciono todo pero no existe user
                    return $_respuestas->error_200("el email $email no existe");
                }
        }
    }

    #ibtener datos del email
    private function obtenerDatosEmail($correo){
        $query = "SELECT email,password,status,id FROM users WHERE email = '$correo'";
        $datos = parent::obtenerDatos($query);

        #para confirmar si existe ese Email de la consulta de arriba, como devuelve solo 
        #un dato, se enumera en 0 y ve si devuelve algo
        if (isset($datos[0]["email"])) {
            return $datos;
        }else{
            return 0;
        }
    }



    #insertar el token
    private function insertarToken($user_id){
        $val = true;
        #combinacion de dos funciones de php para hacer el token, bin2hex  y  openssl_random_pseudo_bytes
        $token = bin2hex(openssl_random_pseudo_bytes(16, $val));
        #se suelen usar las dates asi ya que es el mismo en el que la guardan las DB
        $date = date("Y-m-d H:i");
        $status = 'Active';
        $query = "INSERT INTO users_token (user_id, Token, status, date) VALUES('$user_id', '$token', '$status', '$date')";
        $verifica = parent::nonQuery($query);
        if ($verifica) {
            return $token; 
        }else{
            return 0;
        }
    }


    

}