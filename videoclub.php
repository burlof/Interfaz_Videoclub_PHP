<!-- VIDEOCLUD VERSIÓN 1
     Características de esta versión:
       - Código monolítico (sin arquitectura MVC)
       - Sin seguridad
       - Sin sesiones ni control de acceso
       - Sin reutilización de código
-->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/inicio.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>

<?php

$db = new mysqli("127.0.0.1", "root", "1234", "videoclub");

// Miramos el valor de la variable "action", si existe. Si no, le asignamos una acción por defecto
if (isset($_REQUEST["action"])) {
    $action = $_REQUEST["action"];
} else {
    $action = "mostrarListaPeliculas";  // Acción por defecto
}

//VARIABLES
$ruta_Imagen_Pelicula = "http://localhost/Videoclub_PHP/images/films/";
$ruta_Imagen_Persona = "http://localhost/Videoclub_PHP/images/actors/";
$estilo = "style=width:50px;height:70px;";


// CONTROL DE FLUJO PRINCIPAL
    // El programa saltará a la sección del switch indicada por la variable "action"
    switch ($action) {

        // --------------------------------- MOSTRAR LISTA DE LIBROS ----------------------------------------

        case "mostrarListaPeliculas":
            echo "<h1>Películas y Actores</h1>";

            // Buscamos todas las películas del videoclub
            if ($result = $db->query("SELECT * 
                                        FROM peliculas inner join actuan on peliculas.idPelicula = actuan.idPelicula 
                                        inner join personas on actuan.idPersona = personas.idPersona
                                        ORDER BY peliculas.idPelicula")) {
                                            //inner join personas on peliculas.id_Pelicula = personas.idPersona

                // La consulta se ha ejecutado con éxito. Vamos a ver si contiene registros
                if ($result->num_rows != 0) {
                    // La consulta ha devuelto registros: vamos a mostrarlos

                    // Primero, el formulario de búsqueda
                    echo "<form action='videoclub.php'>
                                <input type='hidden' name='action' value='buscarPeliculas'>
                                <input type='text' name='textoBusqueda'>
                                <input type='submit' value='Buscar'>
                                </form><br>";

                    // Ahora, la tabla con los datos de los libros
                    echo "<table border ='1'>";
                    echo "<tr>
                        <th>ID_Película</th>
                        <th>Título</th>
                        <th>Género</th>
                        <th>País</th>
                        <th>Año</th>
                        <th>Cartel</th>
                        
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Fotografia</th>

                        <th>Update</th>
                        <th>Delete</th>
                    </tr>";
                    while ($fila = $result->fetch_object()) {
                        echo "<tr>";
                        echo "<td>" . $fila->idPelicula . "</td>";
                        echo "<td>" . $fila->titulo . "</td>";
                        echo "<td>" . $fila->genero . "</td>";
                        echo "<td>" . $fila->pais . "</td>";
                        echo "<td>" . $fila->anyo . "</td>";
                        //echo "<td><img src='$ruta_Imagen" . $fila->cartel . "' style=width:40px;height:55px;></td>";
                        echo "<td><img src='$ruta_Imagen_Pelicula" . $fila->cartel . "'$estilo></td>";
                        
                        
                        //echo "<td>" . $fila->idPersona . "</td>";
                        echo "<td>" . $fila->nombre . "</td>";
                        echo "<td>" . $fila->apellidos . "</td>";
                        //echo "<td>" . $fila->fotografia . "</td>";
                        echo "<td><img src='$ruta_Imagen_Persona" . $fila->fotografia . "'$estilo></td>";
                        
                        echo "<td><a href='videoclub.php?action=formularioModificarLibro&idPelicula=" . $fila->idPelicula . "'>Modificar</a></td>";
                        echo "<td><a href='videoclub.php?action=borrarLibro&idPelicula=" . $fila->idPelicula . "'>Borrar</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    // La consulta no contiene registros
                    echo "No se encontraron datos";
                }
            } else {
                // La consulta ha fallado
                echo "Error al tratar de recuperar los datos de la base de datos. Por favor, inténtelo más tarde";
            }
            echo "<p><a href='index.php?action=formularioInsertarLibros'>Nuevo</a></p>";
            break;


            // --------------------------------- BORRAR PELÍCULAS ----------------------------------------

        case "borrarPelicula":
            echo "<h1>Borrar películas</h1>";

            // Recuperamos el id del libro y lanzamos el DELETE contra la BD
            $idPelicula = $_REQUEST["idPelicula"];
            $db->query("DELETE FROM peliculas WHERE idPelicula = '$idPelicula'");

            // Mostramos mensaje con el resultado de la operación
            if ($db->affected_rows == 0) {
                echo "Ha ocurrido un error al borrar el libro. Por favor, inténtelo de nuevo";
            } else {
                echo "Libro borrado con éxito";
            }
            echo "<p><a href='index.php'>Volver</a></p>";

            break;


            // --------------------------------- BUSCAR PELICULAS ----------------------------------------

        case "buscarPeliculas":
            // Recuperamos el texto de búsqueda de la variable de formulario
            $textoBusqueda = $_REQUEST["textoBusqueda"];
            echo "<h1>Resultados de la búsqueda: \"$textoBusqueda\"</h1>";

            // Buscamos los libros de la biblioteca que coincidan con el texto de búsqueda
            if ($result = $db->query("SELECT * FROM libros
					INNER JOIN escriben ON libros.idLibro = escriben.idLibro
					INNER JOIN personas ON escriben.idPersona = personas.idPersona
					WHERE libros.titulo LIKE '%$textoBusqueda%'
					OR libros.genero LIKE '%$textoBusqueda%'
					OR personas.nombre LIKE '%$textoBusqueda%'
					OR personas.apellido LIKE '%$textoBusqueda%'
					ORDER BY libros.titulo")) {

                // La consulta se ha ejecutado con éxito. Vamos a ver si contiene registros
                if ($result->num_rows != 0) {
                    // La consulta ha devuelto registros: vamos a mostrarlos
                    // Primero, el formulario de búsqueda
                    echo "<form action='index.php'>
								<input type='hidden' name='action' value='buscarLibros'>
                            	<input type='text' name='textoBusqueda'>
								<input type='submit' value='Buscar'>
                          </form><br>";
                    // Después, la tabla con los datos
                    echo "<table border ='1'>";
                    while ($fila = $result->fetch_object()) {
                        echo "<tr>";
                        echo "<td>" . $fila->titulo . "</td>";
                        echo "<td>" . $fila->genero . "</td>";
                        echo "<td>" . $fila->numPaginas . "</td>";
                        echo "<td>" . $fila->nombre . "</td>";
                        echo "<td>" . $fila->apellido . "</td>";
                        echo "<td><a href='index.php?action=formularioModificarLibro&idLibro=" . $fila->idLibro . "'>Modificar</a></td>";
                        echo "<td><a href='index.php?action=borrarLibro&idLibro=" . $fila->idLibro . "'>Borrar</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    // La consulta no contiene registros
                    echo "No se encontraron datos";
                }
            } else {
                // La consulta ha fallado
                echo "Error al tratar de recuperar los datos de la base de datos. Por favor, inténtelo más tarde";
            }
            echo "<p><a href='index.php?action=formularioInsertarLibros'>Nuevo</a></p>";
            echo "<p><a href='index.php'>Volver</a></p>";
            break;














































            // --------------------------------- ACTION NO ENCONTRADA ----------------------------------------

        default:
        echo "<h1>Error 404: página no encontrada</h1>";
        echo "<a href='index.php'>Volver</a>";
        break;
    } // switch




?>



</body>