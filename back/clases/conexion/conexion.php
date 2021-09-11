<?php

#se crea la conexion a la base de datos
class conexion{
    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    public $conexion;

    public function __construct()
    {
    $listadatos = $this->datosConexion(); 

    #importar del json para setear valores
    foreach ($listadatos as $key => $value){
        $this->server = $value['server'];
        $this->user = $value['user'];
        $this->password = $value['password'];
        $this->database = $value['database'];
        $this->port= $value['port'];
        }

    $this->conexion = new mysqli($this->server, $this->user, $this->password, $this->database, $this->port);
    if($this->conexion->connect_errno){
        echo "algo va mal con la conexion";
        die();
    } 
    }

    #obtener datos de config y convertirlos a atributo

    private function datosConexion(){
        $direccion = dirname(__FILE__);
        $jsondata = file_get_contents($direccion . "/" . "config");
        #esto toma el json, extrae la informacion y lo devuelve en un array
        return json_decode($jsondata, true);
    }


    #funccion para convertir el array que devuelve la BD en utf8
    private function convertirUTF8($array){
        array_walk_recursive($array, function(&$item,$key){
            #esto lo que hace es ver que no sean caracteres raros y si no, los convierte en utf8
            if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
            }
        });
        return $array;
    }



    #para hacer select 
    public function obtenerDatos($sqlstr){
        $results = $this->conexion->query($sqlstr);
        $resultArray = array();
        foreach ($results as $key) {
            #asi se guarda el resultado de la sentencia sql SELECT  en el array vacio
            $resultArray[] = $key;    
            
        }
        return $this->convertirUTF8($resultArray);
    }




    #esta funcion sirve para hacer los CRUD yllevan ese nombre por convencion MAS QUE TODO LOS INSERTKJ
    public function nonQuery($sqlstr){
        $results = $this->conexion->query($sqlstr);
        return $this->conexion->affected_rows;
    }


    #esta a diferencia de la anterior, es para que cuando se guarde algo, nos devuelva su id
    public function nonQueryId($sqlstr){
        $results = $this->conexion->query($sqlstr);
        $filas = $this->conexion->affected_rows;
        /* var_dump($results); */

        #esto lo que hace es que si se guardo algo, nos devuleve la fila afectada
        if($filas >=1){
            return $this->conexion->insert_id;
        }else{
            return 0;
        }
    }


    #se comprueba la pass recibida con la de la database
    protected function encriptar($password, $DBpassword){
        return password_verify($password, $DBpassword);
    }

}