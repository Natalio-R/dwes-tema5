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

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
session_start();

// Si ya hay un usuario logueado, no debemos mostarle esto
if (isset($_SESSION['usuario'])) {
    header('location:index.php', true, 302);
    exit();
}

// Pedimos el fichero con las funciones
require 'lib/gestionUsuarios.php';

// Si hay POST realizamos el logueo del usuario
if ($_POST) {
    $nombre = isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : '';
    $clave = isset($_POST['clave']) ? htmlspecialchars(trim($_POST['clave'])) : '';

    $esOk = loginUsuario($nombre, $clave);
    if ($esOk) {
        $_SESSION['usuario'] = $nombre;
        header('Location: index.php');
        exit();
    }
}
 
/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: añadir el menú. OK
 * - TODO: formulario con nombre de usuario y contraseña. OK
 */
?>

<a href="./index.php">Volver a la página principal</a>
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