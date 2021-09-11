<?php



#DEBES VER LOS TIPOS DE ERRORES PARA LAS API REST!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!



class respuestas{

    public $response = [
        'status' => "ok",
        "result" => array()
    ];

    #ejemplo de cuando envian algo por un metodo no permitido a la api como un get en vez de post
    public function error_405(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            "error_id" => "405",
            "error_msg" => "metodo no permitido"
        );
        return $this->response;
    }


    #el error 200 no existe obvio, pero puede que envie una respuesta y este mala
    public function error_200($string = "Datos incorrectos"){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            "error_id" => "200",
            "error_msg" => $string
        );
        return $this->response;
    }


    #esto es algo asi como el error BAD REQUEST
    public function error_400(){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            "error_id" => "400",
            "error_msg" => 'datos enviados incompletos o con formato incorrecto'
        );
        return $this->response;
    }



    public function error_500($string = "Error interno del servidor"){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            "error_id" => "500",
            "error_msg" => $string
        );
        return $this->response;
    }


    public function error_401($string = "No autorizado, token invalido"){
        $this->response['status'] = 'error';
        $this->response['result'] = array(
            "error_id" => "401",
            "error_msg" => $string
        );
        return $this->response;
    }
}