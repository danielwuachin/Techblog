<?php

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

    #PARA EL GET!!!!!!!!!!!!!!!!!!!!!!!!!!
    #se iguala pagina a 1 para que sea el predeterminado
    public function listaPacientes($pagina = 1){

        $inicio = 0;
        $cantidad = 100;
        if ($pagina > 1) {
            $inicio = ($cantidad *($pagina - 1 )) +1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id, Nombre, DNI,Telefono,email FROM ". $this->table . " LIMIT $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }



    public function obtenerPaciente($id){
        $query = "SELECT * FROM ". $this->table ." WHERE  id = '$id'";
        return parent::obtenerDatos($query);
    }


    public function obtenerEmail(){
        $query = "SELECT email FROM ". $this->table;
        return parent::obtenerDatos($query);
    }

    public function validarEmail($incomingEmail){
        $emails = $this->obtenerEmail();

        $var = 0;
        foreach($emails as $email){
            
            if($incomingEmail == $email['email']){
                $var = 1;
                return $var;
            }
        }
    }



    #PARA EL POST-----------  HACER CREATE
    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);


        #para comprobar si enviaron el token
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
                

                
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
                    
                    $comprobarEmail = $this->validarEmail($email);
                    if ($comprobarEmail == 1) {
                        return $_respuestas->error_500("Este email ya existe, por favor cambielo");
                    }else{
                        
                        $this->email = $email;
                        /* procesamiento de la imagen */
                        if (isset($datos['icon_path'])) {
                            
                            $icon_path = mysqli_real_escape_string($conexion, $datos['icon_path']);
                            $resp = $this->procesaricon_path($icon_path);
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


            }else{
                return $_respuestas->error_401("el token que se envio es invalido o caduco");
            }
        }
    }


    //se crea apellidos para la icon_path, DIR usa barras asi \ apuntando a la carpeta sin importar donde estes
    private function procesaricon_path($img){
        $apellidos = dirname(__DIR__). "\public\icon_path\\";
        //se debe quitar el base64 de nuestra icon_pathbase64, primero lo que se quita y despues la icon_path
        $partes = explode(";base64,", $img);

        //ahorasacamos el texto del tipo de icon_path, png o jpg
        $extension= explode('/',mime_content_type($img))[1];

        //ahora pasamos el texto de la icon_path
        $icon_path_base64 = base64_decode($partes[1]);

        //luego almacenamos toda la apellidos, un string de nombre unico y la  extension
        $file = $apellidos . uniqid() . "." . $extension;

        //ahora, vamos a la apellidos y guardamos la icon_path
        file_put_contents($file, $icon_path_base64);

        //si quieres modificar la icon_path, se debe hacer aqui antes del return

        //esto se hace para que guarde con las barras que son y que la apellidos sea real
        $nuevaapellidos = str_replace('\\', '/', $file);
        
        return $nuevaapellidos;
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
        $datos = json_decode($json, true);



        #para comprobar si enviaron el token!!!!
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
                
                #comprobamos si todos los datos requeridos nos llegaron
                if (!isset($datos['id'])) {
                    return $_respuestas->error_400();
                }else{

                    $this->id = $datos["id"];
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

                    $email = mysqli_real_escape_string($conexion, $datos['email']);
                    
                    $comprobarEmail = $this->validarEmail($email);
                    if ($comprobarEmail == 1) {
                        return $_respuestas->error_500("Este email ya existe, por favor cambielo");
                    }else{
                        
                        $this->email = $email;
                        /* procesamiento de la imagen */
                        if (isset($datos['icon_path'])) {
                            
                            $icon_path = mysqli_real_escape_string($conexion, $datos['icon_path']);
                            $resp = $this->procesaricon_path($icon_path);
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
        $datos = json_decode($json, true);




        #para comprobar si enviaron el token
        if (!isset($datos['token'])) {
            return $_respuestas->error_401(); 
        }else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
                
                #comprobamos si todos los datos requeridos nos llegaron
                if (!isset($datos['id'])) {
                    return $_respuestas->error_400();
                }else{
                    #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                    $this->id = $datos['id'];


                    #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                    $resp = $this->eliminarPaciente();
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


    private function eliminarPaciente(){
        $query = "DELETE FROM ". $this->table ." WHERE id = '" . $this->id . "'";
        $resp = parent::nonQuery($query);

        if ($resp >= 1) {
            return $resp; 
        }else{
            return 0;
        }
    }


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
    }
}


?>


