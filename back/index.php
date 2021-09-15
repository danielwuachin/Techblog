<?php require_once "clases/conexion/conexion.php";  $conexion = new conexion; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Api from Blog-Videogames</title>
    <link rel="stylesheet" href="assets/estilo.css" type="text/css">
</head>

<body>
    
    <div class="container">
        <h1>Api from Blog-Videogames</h1>
        <h2 class="title">Auth - login</h2>
        <div class="divbody">
            <code>
                POST = /auth
                <br>
                {
                <br>
                "email" :"", -> REQUIRED
                <br>
                "password": "" -> REQUIRED
                <br>
                }

            </code>
        </div>
        <h2 class="title">Users managment</h2>
        <div class="divbody">
            <!-- <h2 class="title">users</h2>
            <code>
                GET /users?page=$numberPage
                <br>
                GET /users?id=$idUser
            </code> -->
            <code>
                POST /users
                <br>
                {
                <br>
                "name" : "", -> REQUIRED
                <br>
                "lastname" : "", -> REQUIRED
                <br>
                "email":"", -> REQUIRED
                <br>
                "password" :"", -> REQUIRED
                <br>
                "icon_path" : "",
                <br>
                "date" : "", -> REQUIRED
                <br>
                "token" : "" -> REQUIRED
                <br>
                }
                
            </code>
            <code>
                PUT /users
                <br>
                {
                <br>
                "id" : "", -> REQUIRED
                <br>
                "name" : "",
                <br>
                "lastname" : "",
                <br>
                "email":"", 
                <br>
                "password" :"", 
                <br>
                "icon_path" : "",
                <br>
                "date" : "", 
                <br>
                "status": "", 
                <br>
                "token" : "" -> REQUIRED
                <br>
                }
            </code>
            <code>
                DELETE /users
                <br>
                {
                <br>
                "id" : "", -> REQUIRED
                <br>
                "token" : "" -> REQUIRED
                <br>
                }
            </code>
        </div>

        <h2 class="title">Category</h2>
        <div class="divbody">
            
            <code>
                GET /category
                <br>
                GET /category?page=$numberPage
                <br>
                GET /Category?id=$idCategory
            </code>

            <code>
                POST = /category
                <br>
                {
                <br>
                "genre" :"", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
            <code>
                PUT = /category
                <br>
                {
                <br>
                "id": "", -> REQUIRED
                <br>
                "genre" :"", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
            <code>
                DELETE = /category
                <br>
                {
                <br>
                "id": "", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
        </div>

        <h2 class="title">Platform</h2>
        <div class="divbody">
            <code>
                GET /platform
                <br>
                GET /platform?page=$numberPage
                <br>
                GET /platform?id=$idPlatform
            </code>
            <code>
                POST = /platform
                <br>
                {
                <br>
                "platform" :"", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }

            </code>
            <code>
                PUT = /platform
                <br>
                {
                <br>
                "id": "", -> REQUIRED
                <br>
                "platform" :"", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
            <code>
                DELETE = /platform
                <br>
                {
                <br>
                "id": "", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
        </div>
        
        <h2 class="title">Publications</h2>
        <div class="divbody">
            <code>
                GET /publications
                <br>
                GET /publications?page=$numberPage
                <br>
                GET /publications?id=$idPublication
            </code>
            <code>
                POST = /publications
                <br>
                {
                <br>
                "category_id" :"",
                <br>
                "platform_id": "", 
                <br>
                
                "title" :"", -> REQUIRED
                <br>
                "description": "" -> REQUIRED
                
                <br>
                "image_path" :"", -> REQUIRED
                <br>
                "date": "" -> REQUIRED
                
                <br>
                "token" :"" -> REQUIRED
                <br>
                }
            </code>
            <code>
                PUT = /publications
                <br>
                {
                <br>
                "id": "", -> REQUIRED
                <br>
                "category_id" :"",
                <br>
                "platform_id": "", 
                <br>
                
                "title" :"", -> REQUIRED
                <br>
                "description": "" -> REQUIRED
                
                <br>
                "image_path" :"", -> REQUIRED
                <br>
                "date": "" -> REQUIRED
                
                <br>
                "token" :"" -> REQUIRED
                <br>
                }
            </code>
            <code>
                DELETE = /publications
                <br>
                {
                <br>
                "id" :"", -> REQUIRED
                <br>
                
                "token" :"" -> REQUIRED
                <br>
                }
                
            </code>
        </div>

        <h2 class="title">Comments</h2>
        <div class="divbody">
            <code>
                GET /comments
                <br>
                GET /comments?page=$numberPage
                <br>
                GET /comments?id=$idComment
            </code>
            <code>
                POST = /comments
                <br>
                {
                <br>
                "publication_id" :"", -> REQUIRED
                <br>
                "date" :"", -> REQUIRED
                <br>
                "content" :"", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
            <code>
                PUT = /comments
                <br>
                {
                <br>
                "id" :"", -> REQUIRED
                <br>
                "publication_id" :"", -> REQUIRED
                <br>
                "date" :"", -> REQUIRED
                <br>
                "content" :"", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
            <code>
                DELETE = /comments
                <br>
                {
                <br>
                "id" :"", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
        </div>
        <h2 class="title">Subcomments</h2>
        <div class="divbody">
            <code>
                GET /subcomments
                <br>
                GET /subcomments?page=$numberPage
                <br>
                GET /subcomments?id=$idComment
            </code>
            <code>
                POST = /subcomments
                <br>
                {
                <br>
                "comment_id" :"", -> REQUIRED
                <br>
                "date" :"", -> REQUIRED
                <br>
                "content" :"", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
            <code>
                PUT = /subcomments
                <br>
                {
                <br>
                "id" :"", -> REQUIRED
                <br>
                "comment_id" :"", -> REQUIRED
                <br>
                "date" :"", -> REQUIRED
                <br>
                "content" :"", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
            <code>
                DELETE = /subcomments
                <br>
                {
                <br>
                "id" :"", -> REQUIRED
                <br>
                "token": "" -> REQUIRED
                <br>
                }
            </code>
        </div>
        <h2 class="title">Logout</h2>
        <div class="divbody">
            <code>
                POST = /logout
                <br>
                {
                <br>
                "token" :"" -> REQUIRED
                <br>
                }
            </code>
        </div>
    </div>

</body>

</html>