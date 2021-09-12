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
    private $categoria_id = "";
    private $plataforma_id = "";
    
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
                if (!isset($datos['titulo']) || !isset($datos['descripcion']) || !isset($datos['image_path']) 
                || !isset($datos['usuario_id']) ) {
                    return $_respuestas->error_400();
                }else{

                    $conexion = $this->conexion;
                    /* var_dump($conexion);die(); */
                    #estos se dejan asi ya que en el if de arriba se confirma su existencia
                    $this->titulo = mysqli_real_escape_string($conexion, $datos['titulo']);
                    $this->descripcion = mysqli_real_escape_string($conexion, $datos['descripcion']); 
                    $this->usuario_id = mysqli_real_escape_string($conexion, $datos['usuario_id']);

                    
                    if(isset($datos['fecha'])) { $this->fecha = mysqli_real_escape_string($conexion, $datos['fecha']); }
                    if(isset($datos['fecha'])) { $this->categoria_id = mysqli_real_escape_string($conexion, $datos['categoria_id']); }
                    if(isset($datos['fecha'])) { $this->categoria_id = mysqli_real_escape_string($conexion, $datos['plataforma_id']); }

                    
                    
                    /* procesamiento de la imagen */
                    if (isset($datos['image_path']) && !is_null($datos['image_path'])  ) {
                        
                        $image_path = mysqli_real_escape_string($conexion, $datos['image_path']);
                        $resp = $_helpers->procesarimage_path($image_path);
                        $this->image_path = $resp;
                    }
                    
                    #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                    $resp = $this->insertarPublicacion();
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


            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }


    //se crea image_path para la image_path, DIR usa barras asi \ apuntando a la carpeta sin importar donde estes
    private function procesarimage_path($img){
        $image_path = dirname(__DIR__). "\public\image_path\\";
        //se debe quitar el base64 de nuestra image_pathbase64, primero lo que se quita y despues la image_path
        $partes = explode(";base64,", $img);

        //ahorasacamos el texto del tipo de image_path, png o jpg
        $extension= explode('/',mime_content_type($img))[1];

        //ahora pasamos el texto de la image_path
        $image_path_base64 = base64_decode($partes[1]);

        //luego almacenamos toda la image_path, un string de titulo unico y la  extension
        $file = $image_path . uniqid() . "." . $extension;

        //ahora, vamos a la image_path y guardamos la image_path
        file_put_contents($file, $image_path_base64);

        //si quieres modificar la image_path, se debe hacer aqui antes del return

        //esto se hace para que guarde con las barras que son y que la image_path sea real
        $nuevaimage_path = str_replace('\\', '/', $file);
        
        return $nuevaimage_path;
    }



    private function insertarPublicacion(){
        $query = "INSERT INTO " . $this->table ." (usuario_id, categoria_id, plataforma_id, image_path, titulo, descripcion,  fecha) 
        VALUES
        ( '" . $this->usuario_id . "', '" . $this->categoria_id . "', '" . $this->plataforma_id . "' , '" . $this->image_path . "' , 
        '" . $this->titulo . "', '" . $this->descripcion . "', '" . $this->fecha . "') ";
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

                    $this->id = $datos["id"];
                    $conexion = $this->conexion;
                    /* var_dump($conexion);die(); */
                    #estos se dejan asi ya que en el if de arriba se confirma su existencia
                    $this->titulo = mysqli_real_escape_string($conexion, $datos['titulo']);

                    /* encriptado de la contraseña */
                    $password = mysqli_real_escape_string($conexion, $datos['password']);
                    $password_segura = password_hash($password, PASSWORD_BCRYPT, ['cost' =>4]);
                    $this->password = $password_segura;

                    

                    if(isset($datos['descripcion'])) { $this->descripcion = mysqli_real_escape_string($conexion, $datos['descripcion']); }
                    if(isset($datos['fecha'])) { $this->fecha = mysqli_real_escape_string($conexion, $datos['fecha']); }

                    $email = mysqli_real_escape_string($conexion, $datos['email']);
                    
                    $comprobarEmail = $this->validarEmail($email);
                    if ($comprobarEmail == 1) {
                        return $_respuestas->error_500("Este email ya existe, por favor cambielo");
                    }else{
                        
                        $this->email = $email;
                        /* procesamiento de la imagen */
                        if (isset($datos['image_path'])) {
                            
                            $image_path = mysqli_real_escape_string($conexion, $datos['image_path']);
                            $resp = $this->procesarimage_path($image_path);
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
        email = '" . $this->email . "', password = '" . $this->password . "', image_path = '" . $this->image_path . "', 
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

/* 
    private function buscarToken(){
        $query = "SELECT  tokenId, UsuarioId, Estado FROM usuarios_token WHERE Token = '" . $this->token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);

        if ($resp) {
            return $resp;
        }else{
            return 0;
        }
    }




    #para actualizar el token cada vez que se realize una consulta
    private function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuarios_token SET Fecha = '$date' WHERE tokenId = '$tokenid'";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        }else{
            return 0;
        }
    } */
}


?>


