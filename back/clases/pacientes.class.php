<?php

require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class pacientes extends conexion{

    
    private $table = "pacientes";
    private $pacienteid = "";
    private $dni = "";
    private $nombre = "";
    private $direccion = "";
    private $codigoPostal = "";
    private $genero = "";
    private $telefono = "";
    private $fechaNacimiento = "";
    private $correo = "";
    private $token = "";
    private $imagen= "";

    #PARA EL GET!!!!!!!!!!!!!!!!!!!!!!!!!!
    #se iguala pagina a 1 para que sea el predeterminado
    public function listaPacientes($pagina = 1){

        $inicio = 0;
        $cantidad = 100;
        if ($pagina > 1) {
            $inicio = ($cantidad *($pagina - 1 )) +1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT PacienteId, Nombre, DNI,Telefono,Correo FROM ". $this->table . " LIMIT $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }



    public function obtenerPaciente($id){
        $query = "SELECT * FROM ". $this->table ." WHERE  PacienteId = '$id'";
        return parent::obtenerDatos($query);
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
                if (!isset($datos['nombre']) || !isset($datos['dni']) || !isset($datos['correo'])) {
                    return $_respuestas->error_400();
                }else{
                    #estos se dejan asi ya que en el if de arriba se confirma su existencia
                    $this->nombre = $datos['nombre'];
                    $this->dni = $datos['dni'];
                    $this->correo = $datos['correo'];

                    if(isset($datos['telefono'])) { $this->telefono = $datos['telefono']; }
                    if(isset($datos['direccion'])) { $this->direccion = $datos['direccion']; }
                    if(isset($datos['codigoPostal'])) { $this->codigoPostal = $datos['codigoPostal']; }
                    if(isset($datos['genero'])) { $this->genero= $datos['genero']; }
                    if(isset($datos['fechaNacimiento'])) { $this->fechaNacimiento = $datos['fechaNacimiento']; }



                    if (isset($datos['imagen'])) {
                        $resp = $this->procesarImagen($datos['imagen']);
                        $this->imagen = $resp;
                        }
                    


                    #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                    $resp = $this->insertarPaciente();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta['result'] = array (
                            "pacienteId" => $resp
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


    //se crea direccion para la imagen, DIR usa barras asi \ apuntando a la carpeta sin importar donde estes
    private function procesarImagen($img){
        $direccion = dirname(__DIR__). "\public\imagenes\\";
        //se debe quitar el base64 de nuestra imagenbase64, primero lo que se quita y despues la imagen
        $partes = explode(";base64,", $img);

        //ahorasacamos el texto del tipo de imagen, png o jpg
        $extension= explode('/',mime_content_type($img))[1];

        //ahora pasamos el texto de la imagen
        $imagen_base64 = base64_decode($partes[1]);

        //luego almacenamos toda la direccion, un string de nombre unico y la  extension
        $file = $direccion . uniqid() . "." . $extension;

        //ahora, vamos a la direccion y guardamos la imagen
        file_put_contents($file, $imagen_base64);

        //si quieres modificar la imagen, se debe hacer aqui antes del return

        //esto se hace para que guarde con las barras que son y que la direccion sea real
        $nuevadireccion = str_replace('\\', '/', $file);
        
        return $nuevadireccion;
    }



    private function insertarPaciente(){
        $query = "INSERT INTO " . $this->table ." (DNI, Nombre, Direccion, CodigoPostal, Telefono, Genero, FechaNacimiento, Correo, Imagen) 
        values
        ('" . $this->dni . "', '" . $this->nombre . "', '" . $this->direccion . "', '" . $this->codigoPostal . "', '" . $this->telefono . "', 
        '" . $this->genero . "', '" . $this->fechaNacimiento . "', '" . $this->correo . "', '" . $this->imagen . "') ";
        $resp = parent::nonQueryId($query);
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
                if (!isset($datos['pacienteid'])) {
                    return $_respuestas->error_400();
                }else{
                    #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                    $this->pacienteid = $datos['pacienteid'];

                    #estos se dejan asi ya que en el if de arriba se confirma su existencia
                    if(isset($datos['nombre'])) { $this->nombre = $datos['nombre']; }
                    if(isset($datos['dni'])) { $this->dni = $datos['dni']; }
                    if(isset($datos['telefono'])) { $this->telefono = $datos['telefono']; }
                    if(isset($datos['correo'])) { $this->correo = $datos['correo']; }
                    if(isset($datos['direccion'])) { $this->direccion = $datos['direccion']; }
                    if(isset($datos['codigoPostal'])) { $this->codigoPostal = $datos['codigoPostal']; }
                    if(isset($datos['genero'])) { $this->genero= $datos['genero']; }
                    if(isset($datos['fechaNacimiento'])) { $this->fechaNacimiento = $datos['fechaNacimiento']; }

                    #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                    $resp = $this->modificarPaciente();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta['result'] = array (
                            "pacienteId" => $this->pacienteid
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



    
    private function modificarPaciente(){
        $query = "UPDATE " . $this->table ." SET Nombre = '" . $this->nombre . "', Direccion =  '" . $this->direccion . "',
        DNI = '" . $this->dni . "', CodigoPostal = '" . $this->codigoPostal . "', Telefono = '" . $this->telefono . "', 
        Genero = '" . $this->genero . "', FechaNacimiento = '" . $this->fechaNacimiento . "', Correo = '" . $this->correo . "'  
        WHERE PacienteId = '" . $this->pacienteid . "'";


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
                if (!isset($datos['pacienteid'])) {
                    return $_respuestas->error_400();
                }else{
                    #como se recibe es el id del campo a actualizar, se guarda en una variable y el resto se verifica aparte
                    $this->pacienteid = $datos['pacienteid'];


                    #EJECUTAR FUNCION GAURDAR CON LOS PARAMETROS RECIEN GUARDADOS ARRIBA
                    $resp = $this->eliminarPaciente();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta['result'] = array (
                            "pacienteId" => $this->pacienteid
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
        $query = "DELETE FROM ". $this->table ." WHERE PacienteId = '" . $this->pacienteid . "'";
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


