<?php
require_once 'clases/pacientes.class.php';
require_once 'clases/respuestas.class.php';

$_respuestas = new respuestas;
$_pacientes = new pacientes;

#para los READ, YA SEAN todos los pacientes o solo uno
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['page'])) {
        $pagina = $_GET['page'];
        $listaPacientes = $_pacientes->listaPacientes($pagina);

        #esto se manda siempre a la cabecera como una respuesta adecuada
        header("Content-Type: application/json");

        #convertir json
        echo json_encode($listaPacientes);

        #esto se manda siempre a la cabecera como una respuesta adecuada
        http_response_code(200);


    }elseif(isset($_GET['id'])){
        $pacienteId = $_GET['id'];
        $datosPaciente = $_pacientes->obtenerPaciente($pacienteId);

        #esto se manda siempre a la cabecera como una respuesta adecuada
        header("Content-Type: application/json");

        #convertir json
        echo json_encode($datosPaciente);

        #esto se manda siempre a la cabecera como una respuesta adecuada
        http_response_code(200);
    }





    #para guardar datos CREATE
}else if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    #recibir los datos enviados por el POST
    $postBody = file_get_contents("php://input");

    #enviamos esto al manejador
    $datosArray = $_pacientes->post($postBody);
    
    #DEVOLVEMOS UNA RESPUESTA AL FRONTEND
    header("Content-Type: application/json");
    if (isset($datosArray['result']['error_id'])) {
        $responseCode = $datosArray['result']['error_id'];
        http_response_code($responseCode);
    }else{
        http_response_code(200);
    }
    #aqui gracias al json_encode se pasa el array a string
    echo json_encode($datosArray);




    #actualizar
}else if ($_SERVER['REQUEST_METHOD'] == 'PUT'){
    #recibir los datos enviados por el POST
    $postBody = file_get_contents("php://input"); 
    
    #enviamos datos al manejador
    $datosArray = $_pacientes->put($postBody);

    #DEVOLVEMOS UNA RESPUESTA AL FRONTEND
    header("Content-Type: application/json");
    if (isset($datosArray['result']['error_id'])) {
        $responseCode = $datosArray['result']['error_id'];
        http_response_code($responseCode);
    }else{
        http_response_code(200);
    }
    #aqui gracias al json_encode se pasa el array a string
    echo json_encode($datosArray);





}else if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){


    #RECIBIR LOS DATOS POR EL HEADER-------- si el frontend es con vuejs, los enviara por ahi
    $headers = getallheaders();
    /* print_r($headers);die(); */
    if (isset($headers['token']) && isset($headers['pacienteid'])) {
        #recibimos los datos por el header
        $send = [
            "token" => $headers['token'],
            "pacienteid" => $headers['pacienteid']
        ];
        #ahora lo convertimos a un JSON para que sea usado
        
        $postBody = json_encode($send);
    }else{
        #recibir los datos enviados por el POST
        $postBody = file_get_contents("php://input"); 

    }



    
        #enviamos datos al manejador
    $datosArray = $_pacientes->delete($postBody);

    #DEVOLVEMOS UNA RESPUESTA AL FRONTEND
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

    header("Content-Type: application/json");
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}