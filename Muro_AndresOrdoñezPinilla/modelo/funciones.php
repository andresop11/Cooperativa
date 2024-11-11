<?php 

//guarda en un fichero con nombre_noticia el contenido que se le pasa por parámetro
function guardar($nombre_noticia,$contenido) {
    
   
    $ok = file_put_contents($nombre_noticia,$contenido);
    return $ok;

}
function editar($nombre_fichero, $contenido)
{
    $ok = file_put_contents($nombre_fichero, $contenido, FILE_APPEND);
    return $ok;
}

function abrir($nombre_fichero)
{

    if (file_exists($nombre_fichero)) {
        return file_get_contents($nombre_fichero);
    } else {
        return false;
    }
}
/*
Funcion existe que recibe el nombre de un usuario y devuelve: 
1. La contraseña del usuario si existe
2. NULL si el usuario no existe */

function existe($usuario)
{

    // Partimos de la hipótesis de que el usuario no va a existir
    $ok = NULL;

    // Cargamos en un array asociativo el fichero de usuarios

    $usuarios = parse_ini_file(".".DIRECTORY_SEPARATOR."usuarios".DIRECTORY_SEPARATOR."usuarios.ini");

    // Preguntamos si existe la clave $usuario dentro del array

    if (isset($usuarios[$usuario])) {

        // Si existe guardamos su contraseña para devolverla

        $ok = $usuarios[$usuario];
    }

    return $ok;
}

// Función 'acceder' que devuelve true si el usuario existe y coincide su contraseña o false en caso contrario

function acceder($usuario, $clave)
{

    // Partimos de la hipótesis de que el usuario no existe

    $ok = false;

    //Llamamos a la función existe para recoger la contraseña del usuario si existe

    // o NULL en caso contrario

    $clave_usuario = existe($usuario);

    // Si es distinto de NULL comparamos con la clave introducida en el formulario

    if ($clave_usuario != NULL && $clave_usuario == $clave) {

        // Si coinciden, devolveremos true como valor indicativo de acceso autorizado

        $ok = true;
    }

    return $ok;
}

// Función que graba un usuario y su clave en el fichero de usuarios

function grabar($usuario, $clave)
{

    // Pensamos que algo puede fallar

    $ok = false;

    // Abrimos el fichero en modo de añadir "a+" Ver los diferentes modos de apertura: https://www.php.net/manual/es/function.fopen.php

    // Abrimos el fichero y obtenemos un descriptor de fichero a través del cual realizar las operaciones de lectura, escritura, cierre, etc

    $f = fopen(".".DIRECTORY_SEPARATOR."usuarios". DIRECTORY_SEPARATOR. "usuarios.ini", "a+");

    // si se ha podido abrir ....

    if ($f != NULL) {

        // Grabamos la línea

        $ok = fwrite($f, "$usuario=$clave" . PHP_EOL); // ok tomará el valor false si no se ha podido grabar

        // Cerramos el fichero

        fclose($f);
    }

    return $ok;
}



// Función 'registrar' que comprueba que el usuario no existe para después añadirlo 
// al fichero de usuarios y crear su directorio de trabajo
// Devuelve true si se ha podido registrar y false en caso contrario 

function registrar($usuario, $clave)
{

    // Pensamos que no se va a poder registrar

    $ok = false;

    // Preguntamos si existe, nos devuelve NULL si el usuario no existe

    if (existe($usuario) == NULL) { // se podría expresar if (!existe($usuario)) { .....

        $ok = grabar($usuario, $clave);

        // Si se ha podido grabar

        if ($ok != false) {

            // Creamos la carpeta con el nombre de usuario

            $ok = mkdir("usuarios" . DIRECTORY_SEPARATOR . $usuario, 0777, true); // La carpeta se crea en el mismo directorio en el que se está ejecutando el script

            // Si todo ha ido bien $ok tendrá el valor true

        }
    }

    return $ok;
}



function leer($ruta) {
    // Inicializamos un array vacío para almacenar los nombres de los directorios
    $contactos = [];
    
    // Escanea el directorio especificado en $ruta y devuelve un array con los nombres de los ficheros y directorios
    $ficheros = scandir($ruta);    
    
    // Iteramos sobre cada elemento del array $ficheros
    foreach($ficheros as $fichero) {
        // Verificamos si el elemento no es un archivo y no son los elementos '.' y '..'
        if (!is_file($fichero) && $fichero != "." && $fichero != "..") {            
            // Añadimos el nombre del directorio al array $contactos
            array_push($contactos, $fichero);
        }
    }

    // Ordenamos el array $contactos alfabéticamente
    asort($contactos);
    
    // Devolvemos el array ordenado de nombres de directorios
    return $contactos;
}

function registrarLog($accion, $mensaje) {
    // Definir la ruta hacia el archivo de log desde la subcarpeta "modelo"
    $archivoLog = __DIR__ . '/../registro.log';
    
    // Obtener la fecha y hora actual en formato 'Y-m-d H:i:s'
    $fechaHora = date("Y-m-d H:i:s");
    
    // Crear el mensaje de log con la fecha y hora, acción y mensaje
    $mensajeLog = "[$fechaHora] Acción: $accion - $mensaje" . PHP_EOL;
    
    // Escribir el mensaje de log en el archivo, añadiendo al final del archivo existente
    file_put_contents($archivoLog, $mensajeLog, FILE_APPEND);
}


