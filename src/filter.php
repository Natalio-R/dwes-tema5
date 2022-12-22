<?php
/*********************************************************************************************************************
 * Este script muestra un formulario a través del cual se pueden buscar imágenes por el nombre y mostrarlas. Utiliza
 * el operador LIKE de SQL para buscar en el nombre de la imagen lo que llegue por $_GET['nombre'].
 * 
 * Evidentemente, tienes que controlar si viene o no por GET el valor a buscar. Si no viene nada, muestra el formulario
 * de búsqueda. Si viene en el GET el valor a buscar (en $_GET['nombre']) entonces hay que preparar y ejecutar una 
 * sentencia SQL.
 * 
 * El valor a buscar se tiene que mantener en el formulario.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */

session_start();

// Si ya hay un usuario logueado, no debemos mostarle esto
if (!isset($_SESSION['usuario'])) {
    header('location:index.php');
    exit();
}

require './lib/gestionUsuarios.php';
$nombre = $_SESSION['usuario'];

function filtra(string $texto): array 
{
    // Conectamos a MariaDB
    $mysqli = new mysqli('db', 'dwes', 'dwes', 'dwes', 3306);
    if ($mysqli->errno) {
        echo "No se ha podido conectar a la base de datos";
        return [];
    }

    // Preparamos la consulta
    $sentencia = $mysqli->prepare(
        "select id, nombre, ruta, subido, usuario from imagen where nombre like ?"
    );
    if (!$sentencia) {
        echo "Error:" . $mysqli->error;
        $mysqli->close();
        return [];
    }

    // Vinculamos (bind)
    $valor = '%' . $texto . '%';
    $vinculo = $sentencia->bind_param('s', $valor);
    if (!$vinculo) {
        echo 'Error al vincular: ' . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }

    // Ejecutamos
    $ejecucion = $sentencia->execute();
    if (!$ejecucion) {
        echo "Error al ejecutar la sentencia: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }

    // Recuperamos las filas obtenidas como resultado
    $resultado = $sentencia->get_result();
    if (!$resultado) {
        echo "Error al obtener los resultados: " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }

    $resultadoBusqueda = [];
    while (($fila = $resultado->fetch_assoc()) != null) {
        $resultadoBusqueda[] = $fila;
    }

    return $resultadoBusqueda;
}

$posts = [];
$textoBuscar = $_GET && isset($_GET['nombre']) ? htmlspecialchars(trim($_GET['nombre'])) : '';

if (mb_strlen($textoBuscar) > 0) {
    $posts = filtra($textoBuscar);
}

?>

<?php
/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar:
 * - TODO: completa el código de la vista añadiendo el menú de navegación.
 * - TODO: en el formulario falta añadir el nombre que se puso cuando se envió el formulario.
 * - TODO: debajo del formulario tienen que aparecer las imágenes que se han encontrado en la base de datos.
 */
?>
<h1>Galería de imágenes</h1>
<?php
if ($nombre == null) {
    echo <<<END
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><strong>Filtrar imágenes</strong></li>
            <li><a href="signup.php">Regístrate</a></li>
            <li><a href="login.php">Iniciar Sesión</a></li>
        </ul>
        END;
    } else {
        echo <<<END
        <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="add.php">Añadir imagen</a></li>
        <li><strong>Filtrar imágenes</strong></li>
        <li><a href="logout.php">Cerrar sesión ($nombre)</a></li>
        </ul>
    END;
}
?>

<h2>Busca imágenes por filtro</h2>

<form method="get">
    <p>
        <label for="nombre">Busca por nombre</label>
        <input type="text" name="nombre" id="nombre" value="<?= $_GET ? $_GET['nombre'] : '' ?>">
    </p>
    <p>
        <input type="submit" value="Buscar">
    </p>
</form>

<?php
foreach ($posts as $post) {
    echo <<<END
        <div>
        <h3>{$post['nombre']}<h3>
        <img src="./imagenes/{$post['nombre']}.jpg" alt="" width="200" />
        </div>
    END;
}
?>