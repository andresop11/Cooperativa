        <?php
        // Inicializamos variables para el nombre del fichero y el contenido
        $nombre_fichero = "";
        $contenido = "";

        // Comprobamos si se ha pulsado el botón relacionado con acciones de publicación
        if (isset($_REQUEST["accionpub"])) {
            // Limpiamos la acción recibida: convertimos a minúsculas y eliminamos espacios
            $accion = str_replace(" ", "", strtolower($_REQUEST["accionpub"]));

            // Evaluamos la acción usando un switch
            switch ($accion) {
                // Caso para publicar una nueva entrada
                case "publicar":
                    // Generamos un nombre de fichero basado en la fecha y hora actual
                    $nombre_fichero = date("U");
                    // Definimos la ruta donde se guardará el fichero de publicación
                    $path = "usuarios" . DIRECTORY_SEPARATOR . $_SESSION["usuario"] . DIRECTORY_SEPARATOR . $nombre_fichero . ".txt";
                    // Intentamos guardar la publicación utilizando la función 'guardar'
                    $ok = guardar($path, $_REQUEST["publicacion"]);

                    // Verificamos si la publicación se guardó correctamente
                    if ($ok == false) {
                        // Si hubo un error, mostramos un mensaje y lo registramos en el log
                        $mensaje = "No se ha podido publicar $path";
                        registrarLog("publicar", "Error al publicar en '$path' por el usuario '" . $_SESSION["usuario"] . "'");
                    } else {
                        // Si la publicación fue exitosa, mostramos un mensaje de éxito
                        $mensaje = "$path se ha publicado correctamente";
                        registrarLog("publicar", "Publicación exitosa en '$path' por el usuario '" . $_SESSION["usuario"] . "'");
                    }
                    // Redirigimos a la vista de publicaciones
                    $vista = "publicaciones.php";
                    break;

                // Caso para eliminar una publicación existente
                case "eliminar":
                    // Recuperamos el nombre del fichero a eliminar
                    $nombre_fichero = $_REQUEST["fichero"];
                    // Definimos la ruta del fichero que queremos eliminar
                    $path = "usuarios" . DIRECTORY_SEPARATOR . $_SESSION["usuario"] . DIRECTORY_SEPARATOR . $nombre_fichero;
                    
                    // Comprobamos si el fichero existe
                    if (file_exists($path)) {
                        // Si existe, lo eliminamos
                        unlink($path);
                        $mensaje = "La publicación se ha borrado"; // Mensaje de éxito
                        registrarLog("eliminar", "Publicación '$nombre_fichero' eliminada por el usuario '" . $_SESSION["usuario"] . "'");
                    } else {
                        // Si no existe, mostramos un mensaje de error
                        $mensaje = "No existe la publicación";
                        registrarLog("eliminar", "Error: La publicación '$nombre_fichero' no existe para el usuario '" . $_SESSION["usuario"] . "'");
                    }
                    // Redirigimos a la vista de publicaciones
                    $vista = "publicaciones.php";
                    break;

                // Caso para responder a una publicación
                case "responder":
                    // Recuperamos el fichero al que se desea responder
                    $fichero = $_REQUEST["fichero"];
                    // Cargamos el contenido de la publicación a la que se está respondiendo
                    $contenido = file_get_contents("usuarios" . DIRECTORY_SEPARATOR . $usuarioActivo . DIRECTORY_SEPARATOR . $fichero);
                    // Establecemos la vista para las respuestas
                    $vista = "respuestas.php";
                    // Registramos la acción de respuesta en el log
                    registrarLog("responder", "Usuario '" . $_SESSION["usuario"] . "' ha respondido en la publicación '$fichero' de '$usuarioActivo'");
                    break;

                // Caso para cancelar una acción
                case "volver":
                    // Simplemente redirigimos a la vista de publicaciones
                    $vista = "publicaciones.php";
                    registrarLog("cancelar", "Usuario '" . $_SESSION["usuario"] . "' canceló una acción.");
                    break;

                // Caso para aceptar una respuesta a una publicación
                case "hacer":
                    // Recuperamos el usuario activo y el nombre del fichero de la respuesta
                    $usuarioActivo = $_REQUEST["usuarioActivo"];
                    $nombre_fichero = $_REQUEST["fichero"];
                    // Definimos la ruta del fichero de la publicación
                    $fichero = "usuarios" . DIRECTORY_SEPARATOR . $usuarioActivo . DIRECTORY_SEPARATOR . $nombre_fichero;
                    // Obtenemos la fecha actual en formato UNIX
                    $segundos = date("U");
                    $fechaFormat = date('d-m-Y', $segundos);
                    // Formateamos la respuesta para agregarla al contenido de la publicación
                    $respuesta = "\n" . $_REQUEST["respuesta"] . " - " . $_SESSION["usuario"] . " - " . $fechaFormat;
                    // Intentamos editar la publicación para agregar la respuesta
                    $ok = editar($fichero, $respuesta);

                    // Verificamos si la edición fue exitosa
                    if ($ok == false) {
                        // Si hubo un error al agregar el comentario
                        $mensaje = "No se ha podido agregar el comentario.";
                        registrarLog("aceptar", "Error al agregar comentario en '$fichero' por el usuario '" . $_SESSION["usuario"] . "'");
                    } else {
                        // Si fue exitoso, mostramos un mensaje de éxito
                        $mensaje = "Se ha agregado el comentario.";
                        registrarLog("aceptar", "Comentario agregado en '$fichero' por el usuario '" . $_SESSION["usuario"] . "'");
                    }
                    
                    // Redirigimos al índice, mostrando las publicaciones del usuario activo
                    header("Location: index.php?ver=$usuarioActivo");
                    break;
            }
        }
        ?>
