                <?php

                // Comprobamos si se ha pulsado el botón relacionado con acciones de usuario
                if (isset($_REQUEST["accionusuarios"])) {
                    // Limpiamos la acción recibida: convertimos a minúsculas y eliminamos espacios
                    $accion = str_replace(" ", "", strtolower($_REQUEST["accionusuarios"]));

                    // Evaluamos la acción usando un switch
                    switch ($accion) {
                        // Caso para acceder con credenciales de usuario
                        case "acceder":
                            // Recuperamos el nombre de usuario y la contraseña del formulario
                            $usuario = $_REQUEST["usuario"]; 
                            $clave = $_REQUEST["clave"]; 
                            // Llamamos a la función 'acceder' para validar las credenciales
                            $ok = acceder($usuario, $clave);

                            // Verificamos si la validación fue exitosa
                            if ($ok) {
                                // Si el acceso es exitoso, guardamos el usuario en la sesión
                                $_SESSION["usuario"] = $usuario; 
                                $usuarioActivo = $_SESSION["usuario"]; // Establecemos el usuario activo
                                $vista = "publicaciones.php"; // Redirigimos a la vista de publicaciones
                                // Registramos el acceso exitoso en el log
                                registrarLog("acceder", "Usuario '$usuario' ha iniciado sesión correctamente.");
                            } else {
                                // Si las credenciales son incorrectas, mostramos un mensaje de error
                                $mensaje = "Usuario incorrecto";
                                $vista = "identificacion.php"; // Redirigimos de nuevo a la identificación
                                // Registramos el intento fallido de acceso en el log
                                registrarLog("acceder", "Intento de acceso fallido para el usuario '$usuario'");
                            }
                            break;

                        // Caso para registrar un nuevo usuario
                        case "registrarme":
                            // Recuperamos el nombre de usuario del formulario
                            $usuario = $_REQUEST["usuario"];
                            // Llamamos a la función 'registrar' para intentar registrar al usuario
                            $ok = registrar($usuario, $_REQUEST["clave"]);
                            
                            // Verificamos si el registro fue exitoso
                            if ($ok) {
                                // Si es exitoso, mostramos un mensaje de éxito
                                $mensaje = "Usuario registrado";
                                // Registramos el registro exitoso en el log
                                registrarLog("registrarme", "Usuario '$usuario' registrado con éxito.");
                            } else {
                                // Si hubo un error, mostramos un mensaje de error
                                $mensaje = "Error: el usuario no se ha podido registrar";
                                // Registramos el error en el log
                                registrarLog("registrarme", "Error al registrar el usuario '$usuario'.");
                            }
                            $vista = "identificacion.php"; // Redirigimos a la vista de identificación
                            break;

                        // Caso para cerrar la sesión del usuario
                        case "cerrarsesión":
                            // Si hay un usuario activo, registramos la acción en el log
                            if (isset($_SESSION["usuario"])) {
                                $usuario = $_SESSION["usuario"];
                                registrarLog("cerrarsesión", "Usuario '$usuario' ha cerrado sesión.");
                            }
                            // Limpiamos y destruimos la sesión actual
                            session_unset(); // Limpiamos todas las variables de sesión
                            session_destroy(); // Destruimos la sesión
                            $vista = "identificacion.php"; // Redirigimos a la vista de identificación
                            break;
                    }
                }
                ?>
