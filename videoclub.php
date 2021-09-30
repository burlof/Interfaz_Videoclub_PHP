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

        // --------------------------------- MOSTRAR LISTA DE PELÍCULAS ----------------------------------------

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

                    // Ahora, la tabla con los datos de las películas
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
                        
                        echo "<td><a href='videoclub.php?action=formularioModificarPelicula&idPelicula=" . $fila->idPelicula . "'>Modificar</a></td>";
                        echo "<td><a href='videoclub.php?action=borrarPelicula&idPelicula=" . $fila->idPelicula . "'>Borrar</a></td>";
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
            echo "<p><a href='videoclub.php?action=formularioInsertarPeliculas'>Nuevo</a></p>";
            break;


            // --------------------------------- BORRAR PELÍCULAS ----------------------------------------

        case "borrarPelicula":
            echo "<h1>Borrar películas</h1>";

            // Recuperamos el id de la película y lanzamos el DELETE contra la BD
            $idPelicula = $_REQUEST["idPelicula"];
            $db->query("DELETE FROM peliculas WHERE idPelicula = '$idPelicula'");

            // Mostramos mensaje con el resultado de la operación
            if ($db->affected_rows == 0) {
                echo "Ha ocurrido un error al borrar la película. Por favor, inténtelo de nuevo";
            } else {
                echo "Película borrada con éxito";
            }
            echo "<p><a href='videoclub.php'>Volver</a></p>";

            break;


            // --------------------------------- BUSCAR PELICULAS ----------------------------------------

        case "buscarPeliculas":
            // Recuperamos el texto de búsqueda de la variable de formulario
            $textoBusqueda = $_REQUEST["textoBusqueda"];
            echo "<h1>Resultados de la búsqueda: \"$textoBusqueda\"</h1>";

            // Buscamos los peliculas de la biblioteca que coincidan con el texto de búsqueda
            if ($result = $db->query("SELECT * FROM peliculas
					INNER JOIN actuan ON peliculas.idPelicula = actuan.idPelicula
					INNER JOIN personas ON actuan.idPersona = personas.idPersona
					WHERE peliculas.titulo LIKE '%$textoBusqueda%'
					OR peliculas.genero LIKE '%$textoBusqueda%'
					OR personas.nombre LIKE '%$textoBusqueda%'
					OR personas.apellidos LIKE '%$textoBusqueda%'
					ORDER BY peliculas.titulo")) {

                // La consulta se ha ejecutado con éxito. Vamos a ver si contiene registros
                if ($result->num_rows != 0) {
                    // La consulta ha devuelto registros: vamos a mostrarlos
                    // Primero, el formulario de búsqueda
                    echo "<form action='videoclub.php'>
								<input type='hidden' name='action' value='buscarPeliculas'>
                            	<input type='text' name='textoBusqueda'>
								<input type='submit' value='Buscar'>
                          </form><br>";
                    // Después, la tabla con los datos
                    echo "<table border ='1'>";
                    while ($fila = $result->fetch_object()) {
                        echo "<tr>";
                        echo "<td>" . $fila->titulo . "</td>";
                        echo "<td>" . $fila->genero . "</td>";
                        //echo "<td>" . $fila->cartel . "</td>";
                        echo "<td><img src='$ruta_Imagen_Pelicula" . $fila->cartel . "'$estilo></td>";
                        echo "<td>" . $fila->nombre . "</td>";
                        echo "<td>" . $fila->apellidos . "</td>";
                        echo "<td><a href='videoclub.php?action=formularioModificarPelicula&idPelicula=" . $fila->idPelicula . "'>Modificar</a></td>";
                        echo "<td><a href='videoclub.php?action=borrarPelicula&idPelicula=" . $fila->idPelicula . "'>Borrar</a></td>";
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
            echo "<p><a href='videoclub.php?action=formularioInsertarPeliculas'>Nuevo</a></p>";
            echo "<p><a href='videoclub.php'>Volver</a></p>";
            break;

            // --------------------------------- FORMULARIO ALTA DE PELÍCULAS ----------------------------------------

        case "formularioInsertarPeliculas":
            echo "<h1>Insertar Películas</h1>";

            // Creamos el formulario con los campos de la película
            echo "<form action = 'videoclub.php' method = 'get'>
                    Título:<input type='text' name='titulo'><br>
                    Género:<input type='text' name='genero'><br>
                    País:<input type='text' name='pais'><br>
                    Año:<input type='text' name='anyo'><br>
                    Cartel:<input type='text' name='cartel'><br>";

            // Añadimos un selector para el id de actores
            $result = $db->query("SELECT * FROM personas");
            echo "Películas: <select name='titulo[]' multiple='true'>";
            while ($fila = $result->fetch_object()) {
                echo "<option value='" . $fila->idPersona . "'>" . $fila->nombre . "</option>";
            }
            echo "</select>";
            echo "<a href='videoclub.php?action=formularioInsertarActores'>Añadir nuevo</a><br>";

            // Finalizamos el formulario
            echo "  <input type='hidden' name='action' value='insertarPelicula'>
					<input type='submit'>
				</form>";
            echo "<p><a href='videoclub.php'>Volver</a></p>";

            break;

            // --------------------------------- INSERTAR PELÍCULAS ----------------------------------------

        case "insertarPelicula":
            echo "<h1>Alta de Películas</h1>";

            // Vamos a procesar el formulario de alta de películas
            // Primero, recuperamos todos los datos del formulario
            $titulo = $_REQUEST["titulo"];
            $genero = $_REQUEST["genero"];
            $pais = $_REQUEST["pais"];
            $anyo = $_REQUEST["anyo"];
            $cartel = $_REQUEST["cartel"];

            $personas = $_REQUEST["nombre"];

            // Lanzamos el INSERT contra la BD.
            echo "INSERT INTO peliculas (titulo,genero,pais,anyo,cartel) VALUES ('$titulo','$genero', '$pais', '$anyo', '$cartel')";
            $db->query("INSERT INTO peliculas (titulo,genero,pais,anyo,cartel) VALUES ('$titulo','$genero', '$pais', '$anyo', '$cartel')");
            if ($db->affected_rows == 1) {
                // Si la inserción de la película ha funcionado, continuamos insertando en la tabla "escriben"
                // Tenemos que averiguar qué idPelicula se ha asignado a la película que acabamos de insertar
                $result = $db->query("SELECT MAX(idPelicula) AS ultimaPelicula FROM peliculas");
                $idPelicula = $result->fetch_object()->ultimaPelicula;
                // Ya podemos insertar todos los actores junto con la película en "actuan"
                
                /*
                foreach ($personas as $idPersona) {
                    $db->query("INSERT INTO actuan(idPelicula, idPersona) VALUES('$idPelicula', '$idPersona')");
                }*/

                echo "Película insertado con éxito";
            } else {
                // Si la inserción de la película ha fallado, mostramos mensaje de error
                echo "Ha ocurrido un error al insertar la película. Por favor, inténtelo más tarde.";
            }
            echo "<p><a href='videoclub.php'>Volver</a></p>";

            break;

            // --------------------------------- FORMULARIO MODIFICAR PELÍCULAS ----------------------------------------

        case "formularioModificarPelicula":
            echo "<h1>Modificación de Películas</h1>";

            // Recuperamos el id de la película que vamos a modificar y sacamos el resto de sus datos de la BD
            $idPelicula = $_REQUEST["idPelicula"];
            $result = $db->query("SELECT * FROM peliculas WHERE peliculas.idPelicula = '$idPelicula'");
            $pelicula = $result->fetch_object();

            // Creamos el formulario con los campos de la película
            // y lo rellenamos con los datos que hemos recuperado de la BD
            echo "<form action = 'videoclub.php' method = 'get'>
				    <input type='hidden' name='idPelicula' value='$idPelicula'>
                    Título:<input type='text' name='titulo' value='$pelicula->titulo'><br>
                    Género:<input type='text' name='genero' value='$pelicula->genero'><br>
                    País:<input type='text' name='pais' value='$pelicula->pais'><br>
                    Año:<input type='text' name='anyo' value='$pelicula->anyo'><br>
                    Número de páginas:<input type='text' name='cartel' value='$pelicula->cartel'><br>";

            // Vamos a añadir un selector para el id del actor o actores.
            // Para que salgan preseleccionados los actores de la pelicula que estamos modificando, vamos a buscar
            // también a esos actores.
            $todosLosActores = $db->query("SELECT * FROM personas");  // Obtener todos los actores
            $actoresPelicula = $db->query("SELECT idPersona FROM actuan WHERE idPelicula = '$idPelicula'");// Obtener solo los actores de la película que estamos buscando
            // Vamos a convertir esa lista de actores de la película en un array de ids de personas
            $listaActoresPelicula = array();
            while ($personas = $actoresPelicula->fetch_object()) {
                $listaActoresPelicula[] = $personas->idPersona;
            }

            // Ya tenemos todos los datos para añadir el selector de actores al formulario
            echo "Actores: <select name='actor[]' multiple size='3'>";
            while ($fila = $todosLosActores->fetch_object()) {
                if (in_array($fila->idPersona, $listaActoresPelicula))
                    echo "<option value='$fila->idPersona' selected>$fila->nombre $fila->apellido</option>";
                else
                    echo "<option value='$fila->idPersona'>$fila->nombre $fila->apellido</option>";
            }
            echo "</select>";

            // Por último, un enlace para crear un nuevo actor
            echo "<a href='videoclub.php?action=formularioInsertarActores'>Añadir nuevo</a><br>";

            // Finalizamos el formulario
            echo "  <input type='hidden' name='action' value='modificarPelicula'>
                    <input type='submit'>
                  </form>";
            echo "<p><a href='videoclub.php'>Volver</a></p>";

            break;

            // --------------------------------- MODIFICAR PELÍCULA ----------------------------------------

        case "modificarPelicula":
            echo "<h1>Modificación de Películas</h1>";

            // Vamos a procesar el formulario de modificación de las películas
            // Primero, recuperamos todos los datos del formulario
            $idPelicula = $_REQUEST["idPelicula"];
            $titulo = $_REQUEST["titulo"];
            $genero = $_REQUEST["genero"];
            $pais = $_REQUEST["pais"];
            $anyo = $_REQUEST["anyo"];
            $cartel = $_REQUEST["cartel"];
            //$nombre = $_REQUEST["nombre"];

            // Lanzamos el UPDATE contra la base de datos.
            $db->query("UPDATE peliculas SET
							titulo = '$titulo',
							genero = '$genero',
							pais = '$pais',
							anyo = '$anyo',
							cartel = '$cartel'
							WHERE idPelicula = '$idPelicula'");

            if ($db->affected_rows == 1) {
                // Si la modificación dela película ha funcionado, continuamos actualizando la tabla "escriben".
                // Primero borraremos todos los registros de la película actual y luego los insertaremos de nuevo
                $db->query("DELETE FROM escriben WHERE idPelicula = '$idPelicula'");
                // Ya podemos insertar todos los actores junto con la película en "actuan"
                /*
                foreach ($personas as $idPersona) {
                    $db->query("INSERT INTO actuan(idPelicula, idPersona) VALUES('$idPelicula', '$idPersona')");
                }
                */
                echo "Película actualizada con éxito";
            } else {
                // Si la modificación de la película ha fallado, mostramos mensaje de error
                echo "Ha ocurrido un error al modificar la película. Por favor, inténtelo más tarde.";
            }
            echo "<p><a href='videoclub.php'>Volver</a></p>";
            break;






            // --------------------------------- ACTION NO ENCONTRADA ----------------------------------------

        default:
        echo "<h1>Error 404: página no encontrada</h1>";
        echo "<a href='videoclub.php'>Volver</a>";
        break;
    } // switch




?>



</body>