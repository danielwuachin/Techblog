<?php
require_once "clases/auth.class.php";
require_once "clases/respuestas.class.php";


#instanciar clases, se usa el _ para saber que la variable es la instancia de una clase
$_auth = new auth;
$_respuestas = new respuestas;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    #recibir datos - obtener el JSON 
    $postBody = file_get_contents('php://input');
    #enviamos los datos al manejador
    $datosArray = $_auth->login($postBody);

    #devolvemos una respuesta
    header("Content-Type: application/json");
    if (isset($datosArray['result']['error_id'])) {
        $responseCode = $datosArray['result']['error_id'];
        http_response_code($responseCode);
    }else{
        http_response_code(200);
    }
    #aqui gracias al json_encode se pasa el array a string
    echo json_encode($datosArray);
    
}else{
    #se manda error 4'5 metodo no permitido
    header("Content-Type: application/json");
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);

}
