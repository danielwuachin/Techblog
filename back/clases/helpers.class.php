<?php
require_once "conexion/conexion.php";


class Helpers extends conexion{
    
    /* hacer pagination */
    public function listar($pagina, $tabla){

        $inicio = 0;
        $cantidad = 5;
        if ($pagina > 1) {
            $inicio = ($cantidad *($pagina - 1 )) +1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id FROM ". $tabla ." LIMIT $inicio, $cantidad" ;
        return parent::obtenerDatos($query);
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
    public function isAdmin($token, $tabla = "users"){
        $query = "SELECT ROLE FROM $tabla WHERE id = ( SELECT user_id FROM users_token WHERE Token = '". $token ."' ) ";
        $resultado = parent::obtenerDatos($query);
        
        return $resultado;
    }




    public function buscarToken($token){
        $query = "SELECT  tokenId, user_id, status FROM users_token WHERE Token = '" . $token . "' AND status = 'Active'";
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
        $query = "UPDATE users_token SET Fecha = '$date' WHERE tokenId = '$tokenid'";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        }else{
            return 0;
        }
    }


    /* eliminar token */
    public function eliminarToken($tokenid){
        $query = "DELETE FROM users_token WHERE Token = '".$tokenid."' ";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        }else{
            return 0;
        } 
    }



    public function obtenerEmail(){
        $query = "SELECT email FROM users";
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



    public function user_id($id, $tabla){
        $query = "SELECT user_id FROM $tabla WHERE id = '". $id . "'";
        var_dump($query);
        $respuesta = parent::obtenerDatos($query);
        $resp = intval($respuesta[0]['user_id']);

        var_dump($respuesta);
        if ($resp){
            return $resp;
        }else{
            return 0;
        }
        
    }
    


    public function userToken($token){
        $query = "SELECT user_id FROM users_token WHERE Token = '".$token."' ";

        $respuesta = parent::obtenerDatos($query);
        $resp = intval($respuesta[0]['user_id']);
        var_dump($resp);
        if ($resp){
            return $resp;
        }else{
            return 0;
        }
    }
}
