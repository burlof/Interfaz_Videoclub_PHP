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
    <link rel="stylesheet" href="css/inicio.css" />
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


// CONTROL DE FLUJO PRINCIPAL
    // El programa saltará a la sección del switch indicada por la variable "action"
    switch ($action) {

        // --------------------------------- MOSTRAR LISTA DE LIBROS ----------------------------------------

        case "mostrarListaPeliculas":
            echo "<h1>Películas</h1>";

            // Buscamos todas las películas del videoclub
            if ($result = $db->query("SELECT * 
                                        FROM peliculas
                                        ORDER BY peliculas.idPelicula")) {

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
                        <th>ID</th>
                        <th>Título</th>
                        <th>Género</th>
                        <th>País</th>
                        <th>Año</th>
                        <th>Cartel</th>
                    </tr>";
                    while ($fila = $result->fetch_object()) {
                        echo "<tr>";
                        echo "<td>" . $fila->idPelicula . "</td>";
                        echo "<td>" . $fila->titulo . "</td>";
                        echo "<td>" . $fila->genero . "</td>";
                        echo "<td>" . $fila->pais . "</td>";
                        echo "<td>" . $fila->anyo . "</td>";
                        echo "<td>" . $fila->cartel . "</td>";
                        //echo "<td><a href='videoclub.php?action=formularioModificarLibro&idPelicula=" . $fila->idPelicula . "'>Modificar</a></td>";
                        //echo "<td><a href='videoclub.php?action=borrarLibro&idPelicula=" . $fila->idPelicula . "'>Borrar</a></td>";
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




        




    } // switch




?>



</body>