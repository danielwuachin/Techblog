<?php
require_once "clases/users.class.php";
require_once "clases/respuestas.class.php";


#instanciar clases, se usa el _ para saber que la variable es la instancia de una clase
$_users = new Users;
$_respuestas = new respuestas;
$_helpers = new Helpers;


#para los READ, YA SEAN todos los users o solo uno
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['pages'])) {
        $pagina = $_GET['page_user'];
        $listausers = $_helpers->listar($pagina, "users");

        #esto se manda siempre a la cabecera como una respuesta adecuada
        header("Content-Type: application/json");

        #convertir json
        echo json_encode($listausers);

        #esto se manda siempre a la cabecera como una respuesta adecuada
        http_response_code(200);


    }elseif(isset($_GET['id_user'])){
        $userId = $_GET['id_user'];
        $datosuser = $_helpers->obtener($userId, "users");

        #esto se manda siempre a la cabecera como una respuesta adecuada
        header("Content-Type: application/json");

        #convertir json
        echo json_encode($datosuser);

        #esto se manda siempre a la cabecera como una respuesta adecuada
        http_response_code(200);
    }elseif(isset($_GET['all'])){
        if($_GET['all'] == 'all'){
            $all = $_helpers->obtenerAll("users");
            #esto se manda siempre a la cabecera como una respuesta adecuada
            header("Content-Type: application/json");

            #convertir json
            echo json_encode($all);

            #esto se manda siempre a la cabecera como una respuesta adecuada
            http_response_code(200);
        }
    }


    #para guardar datos CREATE
}else if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    #recibir los datos enviados por el POST
    $postBody = file_get_contents("php://input");

    #enviamos esto al manejador
    $datosArray = $_users->post($postBody);
    
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
    $datosArray = $_users->put($postBody);

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
    if (isset($headers['token']) && isset($headers['id'])) {
        #recibimos los datos por el header
        $send = [
            "token" => $headers['token'],
            "id" => $headers['id']
        ];
        #ahora lo convertimos a un JSON para que sea usado
        
        $postBody = json_encode($send);
    }else{
        #recibir los datos enviados por el POST
        $postBody = file_get_contents("php://input"); 

    }



    
        #enviamos datos al manejador
    $datosArray = $_users->delete($postBody);

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