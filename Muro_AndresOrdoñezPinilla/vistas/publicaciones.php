        <section>
            <form>
                <!-- Botón para cerrar sesión, que enviará una solicitud al servidor -->
                <input type="submit" value="Cerrar sesión" name="accionusuarios"/>
            </form>
            <br><br>

            <?php
            // Llama a la función 'leer' para obtener los usuarios registrados
            $usuarioRegistrado=leer("usuarios"); ?>

            <ol>
            <?php
            // Itera sobre cada usuario registrado
            foreach ($usuarioRegistrado as $registrado):
                    // Comprueba si el usuario actual no es el mismo que el usuario en sesión
                    if ($registrado != $_SESSION["usuario"]): ?>
                    <!-- Muestra un enlace al perfil del usuario registrado -->
                    <li><a href="?ver=<?php echo $registrado; ?>"><?php echo $registrado; ?></a></li>
                <?php
                    endif; 
                endforeach; 
                ?>
            </ol>
        </section>

        <section>
            <?php 
            // Verifica si el usuario activo es el mismo que el de la sesión
            if ($usuarioActivo == $_SESSION["usuario"]): ?>
                <form method="post">
                    <!-- Área de texto para que el usuario publique un mensaje -->
                    <textarea name="publicacion" id="publicacion" cols="50" rows="5" placeholder="¿Qué deseas publicar?"></textarea> <br><br>
                    <!-- Botón para enviar la publicación -->
                    <input type="submit" name="accionpub" value="Publicar">
                </form>
            <?php endif; ?>
            
            <?php
            // Lee los archivos del usuario activo y los ordena en orden descendente
            $ficheros = leer("usuarios" . DIRECTORY_SEPARATOR . $usuarioActivo);
            arsort($ficheros);
            
            // Itera sobre los archivos del usuario activo
            foreach ($ficheros as $fichero):
                // Lee el contenido del archivo
                $contenido = file_get_contents("usuarios" . DIRECTORY_SEPARATOR . $usuarioActivo . DIRECTORY_SEPARATOR . $fichero);
                // Extrae el timestamp del nombre del archivo y lo convierte a formato de fecha
                $segundos = (int) substr($fichero, 0, -4);
                $fechaFormat = date('d-m-Y', $segundos);
                ?>
                <section class="publicacion">
                    <h3>Publicación:</h3>
                    <!-- Muestra el contenido de la publicación -->
                    <pre><?php echo $contenido; ?></pre>
                    <br>
                    <h5>Creado por: <?php echo $usuarioActivo; ?></h5>
                    
                    <form method="post">
                        <!-- Botón para responder a la publicación -->
                        <input type="submit" name="accionpub" value="Responder">
                        <!-- Campo oculto para identificar el archivo al que se está respondiendo -->
                        <input type="hidden" name="fichero" value="<?php echo $fichero; ?>">
                        <?php 
                        // Si el usuario activo es el mismo que el de la sesión, muestra el botón de eliminar
                        if ($usuarioActivo == $_SESSION["usuario"]): ?>
                            <input type="submit" name="accionpub" value="Eliminar">
                        <?php endif; ?>
                    </form>
                </section>
            <?php endforeach; ?>
            
            <?php 
            // Si el usuario activo no es el mismo que el de la sesión, muestra un enlace a sus propias publicaciones
            if ($usuarioActivo != $_SESSION["usuario"]): ?>
                <a href="?ver=<?php echo $_SESSION["usuario"]; ?>"> <<<< MIS PUBLICACIONES >>>> </a>
            <?php endif; ?>
        </section>
