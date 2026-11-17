<?php
session_start();
require_once 'conexion.php';

$accion = $_POST['accion'] ?? '';

if ($accion === 'registro') {
    $nombre = pg_escape_string($conexion, trim($_POST['nombre']));
    $email = pg_escape_string($conexion, trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $check = pg_query_params($conexion, "SELECT id_usuario FROM jh_usuarios WHERE email = $1", array($email));
    if ($check && pg_num_rows($check) > 0) {
        header("Location: index.php?error=email_existente");
        exit();
    }

    // Si registras un correo que contenga la palabra 'admin', te da rol de administrador automáticamente
    $rol = (strpos(strtolower($email), 'admin') !== false) ? 'admin' : 'cliente';

    $query = "INSERT INTO jh_usuarios (nombre, email, password, rol) VALUES ($1, $2, $3, $4)";
    $res = pg_query_params($conexion, $query, array($nombre, $email, $password, $rol));

    if ($res) {
        $_SESSION['usuario'] = $nombre;
        $_SESSION['email'] = $email;
        $_SESSION['rol'] = $rol;
        header("Location: index.php?login=success");
    } else {
        header("Location: index.php?error=registro_fallido");
    }
    exit();
} 
elseif ($accion === 'login') {
    $email = pg_escape_string($conexion, trim($_POST['email']));
    $password = $_POST['password'];

    $query = "SELECT * FROM jh_usuarios WHERE email = $1";
    $res = pg_query_params($conexion, $query, array($email));

    if ($res && pg_num_rows($res) > 0) {
        $user = pg_fetch_assoc($res);
        if (password_verify($password, $user['password'])) {
            $_SESSION['usuario'] = $user['nombre'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['rol'] = $user['rol'];
            header("Location: index.php");
            exit();
        }
    }
    header("Location: index.php?error=credenciales_invalidas");
    exit();
}
elseif ($accion === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>