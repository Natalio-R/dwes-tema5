<?php

/**
 * Lee la base de datos con los usuarios registrados en busca del
 * usuario indicado como argumento.
 * 
 * Devuelve true si existe y false en caso contrario.
 */
function existeUsuario(string $nombre): bool
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli -> connect_errno) {
        echo "No ha sido posible conectarse a la base de datos";
        exit();
    }

    $resultado = $mysqli->query(
        "select nombre from usuario where nombre like '$nombre'"
    );

    if (!$resultado) {
        return false;
    }

    foreach ($resultado as $nombreRegistrado) {
        if ($nombreRegistrado == $nombre) {
            return true;
        }
    }

    $resultado->free();
    $mysqli->close();

    return false;
}


/**
 * Devuelve un array con el usaurio o un array vacío si no existe.
 */
function getUsuario(string $nombre): array
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli -> connect_errno) {
        echo "No ha sido posible conectarse a la base de datos";
        exit();
    }

    $resultado = $mysqli->query(
        "select nombre, clave from usuario where nombre = '$nombre'"
    );

    $row = [];
    if (!$resultado) {
        echo "No se han obtenido datos";
    } else {
        $row = $resultado->fetch_assoc();
    }
    
    $resultado->free();
    $mysqli->close();

    return $row == null ? [] : $row;
}

/**
 * Inserta en la base de datos de usuarios al usuario con la clave indicada.
 */
function insertUsuario(string $nombre, string $clave)
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli -> connect_errno) {
        echo "No ha sido posible conectarse a la base de datos";
        exit();
    }

    $claveCifrada = password_hash($clave, PASSWORD_BCRYPT);

    $resultado = $mysqli->query(
        "insert into usuario (nombre, clave) values ('$nombre', '$claveCifrada')"
    );

    if (!$resultado) {
        return [];
    }

    $mysqli->close();
}


/**
 * Realiza la validación del nuevo usuario y devuelve un array vacío si no hay
 * errores y un array de arrays con los errores.
 */
function validaRegistro(string $nombre, string $clave, string $repiteClave): array
{
    $errores = [];

    if (empty($nombre)) {
        $errores['nombre'] = 'Debes introducir un nombre';
    } else if (!ctype_alnum($nombre)) {
        $errores['nombre'] = 'El nombre de usuario solo puede contener caracteres alfanuméricos';
    } else if (existeUsuario($nombre)) {
        $errores['nombre'] = 'Nombre de usuario no disponible';
    }

    if (strlen($clave) < 8) {
        $errores['clave'] = 'La contraseña debe ser de 8 caracteres como mínimo';
    } else if ($clave != $repiteClave) {
        $errores['clave'] = 'Las contraseñas no coinciden';
    }

    return $errores;
}


/**
 * Realiza el registro del nuevo usuario con los datos enviados por argumento.
 * 
 * Si el registro se lleva a cabo sin problemas devuelve null.
 * Si hay errores envía un array de arrays con los errores.
 */
function registroUsuario(string $nombre, string $clave, string $repiteClave): array|null
{
    $errores = validaRegistro($nombre, $clave, $repiteClave);

    if (empty($errores)) {
        insertUsuario($nombre, $clave);
    }

    return !empty($errores) ? $errores : null;
}


/**
 * Realiza el "login" del usuario.
 * 
 * Devuelve true si existe un usuario con la clave indicada y false en caso
 * contrario.
 */
function loginUsuario(string $usuario, string $clave): bool
{
    $usuarioRegistrado = getUsuario($usuario);
    $hash = $usuarioRegistrado['clave'];

    if (!empty($usuarioRegistrado) && password_verify($clave, $hash)) {
        return true;
    } else {
        return false;
    }
}
