<?php
require_once "conexion/conexion.php";

class Helpers extends conexion{
    /* hacer pagination */
    public function listar($pagina = 1, $tabla){

        $inicio = 0;
        $cantidad = 100;
        if ($pagina > 1) {
            $inicio = ($cantidad *($pagina - 1 )) +1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id, Nombre, DNI,Telefono,email FROM ". $tabla . " LIMIT $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return $datos;
    }


    /* obtener solo uno */
    public function obtener($id, $tabla){
        $query = "SELECT * FROM ". $tabla ." WHERE  id = '$id'";
        return parent::obtenerDatos($query);
    }
    
    /* obtener todo */
    public function obtenerAll($tabla){
        $query = "SELECT * FROM ". $tabla ;
        return parent::obtenerDatos($query);
    }


    /* verificar si es admin */
    public function isAdmin($token, $tabla = "usuarios"){
        $query = "SELECT ROLE FROM $tabla WHERE id = ( SELECT UsuarioId FROM usuarios_token WHERE Token = '". $token ."' ) ";
        $resultado = parent::obtenerDatos($query);
        var_dump($resultado);
        return $resultado;
    }




    public function buscarToken($token){
        $query = "SELECT  tokenId, UsuarioId, Estado FROM usuarios_token WHERE Token = '" . $token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);

        if ($resp) {
            return $resp;
        }else{
            return 0;
        }
    }

    #para actualizar el token cada vez que se realize una consulta
    public function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuarios_token SET Fecha = '$date' WHERE tokenId = '$tokenid'";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        }else{
            return 0;
        }
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



        //se crea image_path para la image_path, DIR usa barras asi \ apuntando a la carpeta sin importar donde estes
        public function procesarimage_path($img){
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
    
    
}
