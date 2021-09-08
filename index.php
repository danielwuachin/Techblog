<?php require_once "clases/conexion/conexion.php";  $conexion = new conexion; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API - Prubebas</title>
    <link rel="stylesheet" href="assets/estilo.css" type="text/css">
</head>

<body>
    
    <div class="container">
        <h1>Api de pruebas</h1>
        <div class="divbody">
            <h3>Auth - login</h3>
            <code>
                POST /auth
                <br>
                {
                <br>
                "usuario" :"", -> REQUERIDO
                <br>
                "password": "" -> REQUERIDO
                <br>
                }

            </code>
        </div>
        <div class="divbody">
            <h3>Pacientes</h3>
            <code>
                GET /pacientes?page=$numeroPagina
                <br>
                GET /pacientes?id=$idPaciente
            </code>
            <!-- // $query = "INSERT INTO pacientes (DNI)value('1')" ;
 --> <code>
                POST /pacientes
                <br>
                {
                <br>
                "nombre" : "", -> REQUERIDO
                <br>
                "dni" : "", -> REQUERIDO
                <br>
                "correo":"", -> REQUERIDO
                <br>
                "codigoPostal" :"",
                <br>
                "genero" : "",
                <br>
                "telefono" : "",
                <br>
                "fechaNacimiento" : "",
                <br>
                "token" : "" -> REQUERIDO
                <br>
                }
                <!-- // print_r($conexion->nonQueryId($query));
 -->
            </code>
            <code>
                PUT /pacientes
                <br>
                {
                <br>
                "nombre" : "",
                <br>
                "dni" : "",
                <br>
                "correo":"",
                <br>
                "codigoPostal" :"",
                <br>
                "genero" : "",
                <br>
                "telefono" : "",
                <br>
                "fechaNacimiento" : "",
                <br>
                "token" : "" , -> REQUERIDO
                <br>
                "pacienteId" : "" -> REQUERIDO
                <br>
                }
                <p>
                    

"nombre" : "perraaa", <br/>
"apellidos" : "rodriguez",<br/> 
"email":"jakjsdhlkad@lkadsf@oma.com",<br/>
"password" :"123456",<br/>
"icon_path": "",<br/>
"fecha": "2020-01-03",<br/>

"Estado": "Activo",<br/>
"token" : "ac9df1eb83738f5ef7bd8ceac80dd261" ,<br/>
"id": "14"

                </p>
                ?>
            </code>
            <code>
                DELETE /pacientes
                <br>
                {
                <br>
                "token" : "", -> REQUERIDO
                <br>
                "pacienteId" : "" -> REQUERIDO
                <br>
                }
            </code>
        </div>
        hola index
    </div>

</body>

</html>