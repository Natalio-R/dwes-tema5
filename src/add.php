<?php
/**********************************************************************************************************************
 * Este es el script que añade imágenes en la base de datos. En la tabla "imagen" de la base de datos hay que guardar
 * el nombre que viene vía POST, la ruta de la imagen como se indica más abajo, la fecha de la inserción (función
 * UNIX_TIMESTAMP()) y el identificador del usuario que inserta la imagen (el usuario que está logeado en estos
 * momentos).
 * 
 * ¿Cuál es la ruta de la imagen? ¿De dónde sacamos esta ruta? Te lo explico a continuación:
 * - Busca una forma de asignar un nombre que sea único.
 * - La extensión será la de la imagen original, que viene en $_FILES['imagne']['name'].
 * - Las imágenes se subirán a la carpeta llamada "imagenes/" que ves en el proyecto.
 * - En la base de datos guardaremos la ruta relativa en el campo "ruta" de la tabla "imagen".
 * 
 * Así, si llega por POST una imagen PNG y le asignamosel nombre "imagen1", entonces en el campo "ruta" de la tabla
 * "imagen" de la base de datos se guardará el valor "imagenes/imagen1.png".
 * 
 * Como siempre:
 * 
 * - Si no hay POST, entonces tan solo se muestra el formulario.
 * - Si hay POST con errores se muestra el formulario con los errores y manteniendo el nombre en el campo nombre.
 * - Si hay POST y todo es correcto entonces se guarda la imagen en la base de datos para el usuario logeado.
 * 
 * Esta son las validaciones que hay que hacer sobre los datos POST y FILES que llega por el formulario:
 * - En el nombre debe tener algo (mb_strlen > 0).
 * - La imagen tiene que ser o PNG o JPEG (JPG). Usa FileInfo para verificarlo.
 * 
 * NO VAMOS A CONTROLAR SI YA EXISTE UNA IMAGEN CON ESE NOMBRE. SI EXISTE, SE SOBREESCRIBIRÁ Y YA ESTÁ.
 * 
 * A ESTE SCRIPT SOLO SE PUEDE ACCEDER SI HAY UN USARIO LOGEADO.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que desarrollar toda la lógica de este script.
 */
session_start();

// Si ya hay un usuario logueado, no debemos mostarle esto
if (!isset($_SESSION['usuario'])) {
    header('location:index.php');
    exit();
}

require './lib/gestionUsuarios.php';
$nombre = $_SESSION['usuario'];

function añadirImagen(string $nombreI, string $ruta, int $usuario)
{
    // Conectamos a la base de datos
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli -> connect_errno) {
        echo "No ha sido posible conectarse a la base de datos";
        exit();
    }

    // Preparamos la consulta
    $resultado = $mysqli->prepare(
        "insert into imagen(nombre, ruta, subido, usuario) value (?, ?, UNIX_TIMESTAMP(), ?);"
    );
    if ($resultado === false) {
        echo "No se ha podido ";
        echo $mysqli->error;
        $mysqli->close();
    }

    //Vinculamos (bind)
    $dato1 = $nombreI;
    $dato2 = $ruta;
    $dato3 = $usuario;
    $vinculo = $resultado->bind_param('ssi', $dato1, $dato2, $dato3);
    if (!$vinculo) {
        echo 'Error al vincular: ' . $mysqli->error;
        $resultado->close();
        $mysqli->close();
    }

    //Ejecutamos
    $ejecucion = $resultado->execute();
    if (!$ejecucion) {
        echo "Error al ejecutar la sentencia: " . $mysqli->error;
        $resultado->close();
        $mysqli->close();
    }
}

if ($_POST) {
    $fichero = $_FILES['imagen'];
    $nombreInsert = htmlspecialchars(trim($_POST['nombre']));
    $extension = pathinfo($fichero["name"], PATHINFO_EXTENSION);
    $rutaInsert = "/imagenes/" . $nombreInsert . ".". $extension;
    $usser = 2;
    añadirImagen($nombreInsert, $rutaInsert, $usser);
}


function validarNombre()
{
  if ($_POST) {
    $fichero = $_FILES['imagen'];
    $nombreValidado = htmlspecialchars(trim($_POST['nombre']," "));
    $patronTexto = "/^[0-9a-zA-ZáéíóúÁÉÍÓÚäëïöüÄËÏÖÜàèìòùÀÈÌÒÙ\s]+$/";
    $extension = pathinfo($fichero["name"], PATHINFO_EXTENSION);

    if (!empty($_POST)) {
      if ($extension == "png" || $extension == "jpg" || $extension == "jpeg") {
        if (isset($_POST['nombre']) && mb_strlen($_POST['nombre']) > 0) {
          if (empty($_POST['nombre'])) {
            echo "El nombre del fichero no puede estar vacío";
          } else {
            if (preg_match($patronTexto, $_POST['nombre'])) {
              $nombreValidado = htmlspecialchars(trim($_POST['nombre']," "));
              $rutaDestino = "imagenes/" . $nombreValidado . "." . $extension;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
                echo "El fichero " . $nombreValidado . " se ha subido correctamente";
            } else {
                echo "¡El nombre sólo puede contener letras y espacios!";
            }
          }
        } else {
          echo "¡No se han especificado todos los campos requeridos!";
        }
      } else {
        echo "El tipo de archivo no es compatible. Los ficheros han de ser .png, .jpg o .jpeg";
      }
    }
  }
}


/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar:
 * - TODO: añadir el menú de navegación.
 * - TODO: añadir en el campo del nombre el valor del mismo cuando haya errores en el envío para mantener el nombre
 *         que el usuario introdujo.
 * - TODO: añadir los errores que se produzcan cuando se envíe el formulario debajo de los campos.
 */
?>
<h1>Galería de imágenes</h1>
<?php
if ($nombre == null) {
    echo <<<END
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="signup.php">Regístrate</a></li>
            <li><a href="login.php">Iniciar Sesión</a></li>
        </ul>
        END;
    } else {
        echo <<<END
        <ul>
        <li><a href="index.php">Home</a></li>
        <li><strong>Añadir imagen</strong></li>
        <li><a href="filter.php">Filtrar imágenes</a></li>
        <li><a href="logout.php">Cerrar sesión ($nombre)</a></li>
        </ul>
    END;
}
?>

<form method="post" enctype="multipart/form-data">
    <p>
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" value="<?= $_POST ? $_POST['nombre'] : '' ?>">
    </p>

    <p>
        <label for="imagen">Imagen</label>
        <input type="file" name="imagen" id="imagen">
    </p>
    <?php echo validarNombre(); ?>

    <p>
        <input type="submit" value="Añadir">
    </p>
</form>
