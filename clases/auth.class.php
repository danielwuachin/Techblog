<?php

require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class auth extends conexion{

    #json recibido de la pagina auth.php
    public function login($json){
        $_respuestas = new respuestas;
        #convertirn  json a un array

        $datos = json_decode($json, true);

        #verificar que quien consume la api tiene un campo user y otro pass correctamente
        if (!isset($datos['usuario']) || !isset($datos['password'])) {
            #error por no tener los campos
            return $_respuestas->error_400();

        }else{
            // todo bien todo correcto
            $usuario = $datos['usuario'];
            #se encripta para compararla con la de la base de datos
            $password = $datos['password'];
            
            $datos = $this->obtenerDatosUsuario($usuario);
            var_dump($datos); 

            $password = parent::encriptar($password, $datos[0]['password']);
            var_dump($password);
            #ver si hay datos
            if ($datos) {
                # verificar si la contraseña ingresada es igual a la de la DDBB
                if ($password == $datos[0]['password']) {
                    
                    if ($datos[0]['Estado'] == 'Activo') {
                        #crear el token
                        $verificar = $this->insertarToken($datos[0]['UsuarioId']);
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
                        #error, usuario no esta activo
                        return $_respuestas->error_200("el usuario no esta activo mi pana");
                    }
                }else{
                    return $_respuestas->error_200("el password es invalido");
                }
                
            }else{
                #no eciste el usuario, se usa error 200 ya que si funciono todo pero no existe user
                return $_respuestas->error_200("el usuario $usuario no existe");
            }
        }
    }

    #ibtener datos del usuario
    private function obtenerDatosUsuario($correo){
        $query = "SELECT email,password FROM usuarios WHERE email = '$correo'";
        $datos = parent::obtenerDatos($query);

        #para confirmar si existe ese usuario de la consulta de arriba, como devuelve solo 
        #un dato, se enumera en 0 y ve si devuelve algo
        if (isset($datos[0]["email"])) {
            return $datos;
        }else{
            return 0;
        }
    }



    #insertar el token
    private function insertarToken($usuarioId){
        $val = true;
        #combinacion de dos funciones de php para hacer el token, bin2hex  y  openssl_random_pseudo_bytes
        $token = bin2hex(openssl_random_pseudo_bytes(16, $val));
        #se suelen usar las fechas asi ya que es el mismo en el que la guardan las DB
        $date = date("Y-m-d H:i");
        $estado = 'Activo';
        $query = "INSERT INTO usuarios_token (UsuarioId, Token, Estado, Fecha) VALUES('$usuarioId', '$token', '$estado', '$date')";
        $verifica = parent::nonQuery($query);
        if ($verifica) {
            return $token; 
        }else{
            return 0;
        }
    }

}