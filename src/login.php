<?php
/**********************************************************************************************************************
 * Este programa, a través del formulario que tienes que hacer debajo, en el área de la vista, realiza el inicio de
 * sesión del usuario verificando que ese usuario con esa contraseña existe en la base de datos.
 * 
 * Para mantener iniciada la sesión dentrás que usar la $_SESSION de PHP.
 * 
 * En el formulario se deben indicar los errores ("Usuario y/o contraseña no válido") cuando corresponda.
 * 
 * Dicho formulario enviará los datos por POST.
 * 
 * Cuando el usuario se haya logeado correctamente y hayas iniciado la sesión, redirige al usuario a la
 * página principal.
 * 
 * UN USUARIO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */

session_start();

// Si ya hay un usuario logueado, no debemos mostarle esto
if (isset($_SESSION['usuario'])) {
    header('location:index.php', true, 302);
    exit();
}

require 'lib/gestionUsuarios.php';

if ($_POST) {
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $clave = isset($_POST['clave']) ? $_POST['clave'] : '';

    $esOk = loginUsuario($nombre, $clave);
    if ($esOk) {
        $_SESSION['nombre'] = $nombre;
        header('location: index.php');
        exit();
    }
}


/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */

 
/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: añadir el menú.
 * - TODO: formulario con nombre de usuario y contraseña.
 */
?>

<h1>Inicia sesión</h1>
<form action="login.php" method="post">
    <p>
        <label for="nombre">Nombre de usuario</label>
        <input type="text" name="nombre" id="nombre">
    </p>
    <p>
        <label for="clave">Contraseña</label>
        <input type="password" name="clave" id="clave">
    </p>
    <p>
        <input type="submit" value="Iniciar Sesión">
    </p>
</form>