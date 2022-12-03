<?php
/*********************************************************************************************************************
 * Este script realiza el registro del usuario vía el POST del formulario que hay debajo, en la vista.
 * 
 * Cuando llegue POST hay que validarlo y si todo fue bien insertar en la base de datos el usuario.
 * 
 * Requisitos del POST:
 * - El nombre de usuario no tiene que estar vacío y NO PUEDE EXISTIR UN USUARIO CON ESE NOMBRE EN LA BASE DE DATOS.
 * - La contraseña tiene que ser, al menos, de 8 caracteres.
 * - Las contraseñas tiene que coincidir.
 * 
 * La contraseña la tienes que guardar en la base de datos cifrada mediante el algoritmo BCRYPT.
 * 
 * UN USUARIO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
session_start();

// Si ya hay un usuario logueado, no debemos mostarle esto
if (isset($_SESSION['nombre'])) {
    header('location:index.php');
    exit();
}

require './lib/gestionUsuarios.php';

if ($_POST) {
    $errores = registroUsuario(
        isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : '',
        isset($_POST['clave']) ? htmlspecialchars(trim($_POST['clave'])) : '',
        isset($_POST['repite_clave']) ? htmlspecialchars(trim($_POST['repite_clave'])) : ''
    );
}

/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: los errores que se produzcan tienen que aparecer debajo de los campos.
 * - TODO: cuando hay errores en el formulario se debe mantener el valor del nombre de usuario en el campo
 *         correspondiente.
 */
?>
<h1>Regístrate</h1>

<?php if (!$_POST || ($_POST && $errores)) { ?>
<form action="signup.php" method="post">
    <p>
        <label for="nombre">Nombre de usuario</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo $_POST && isset($_POST['nombre']) ? $_POST['nombre'] : ''; ?>">
        <?php 
        if (isset($errores) && isset($errores['nombre'])) {
            echo "<br>".$errores['nombre'];
        }
        ?>
    </p>
    <p>
        <label for="clave">Contraseña</label>
        <input type="password" name="clave" id="clave">
    </p>
    <p>
        <label for="repite_clave">Repite la contraseña</label>
        <input type="password" name="repite_clave" id="repite_clave">
        <?php 
        if (isset($errores) && isset($errores['clave'])) {
            echo "<br>".$errores['clave'];
        }
        ?>
    </p>
    <p>
        <input type="submit" value="Regístrate">
    </p>
</form>
<?php } else { ?>
    <h3>¡Te has registrado!</h3>
    <a href="index.php">Voler a la página principal</a>
<?php } ?>