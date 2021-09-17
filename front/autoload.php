<?php

/* 
por convencion se llama app_autoloader()
 * el mago es  spl_autoload_register    ya que lo que hace es utilizar esta funcionpara buscar todas las clases que estan en la
 * variable de su funcion  ()
 */


function controllers_autoload($classname){
	include 'controllers/' . $classname . '.php';
}

spl_autoload_register('controllers_autoload');

// esta funcion lo que hace es incluir todas las clases que esten en el directorio de la funcion dada-

