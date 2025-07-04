<?php
// Iniciar la sesión para poder acceder a ella.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpiar el array de sesión.
$_SESSION = array();

// Invalidar la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
session_destroy();

// Redirigir al login con un mensaje de éxito (opcional).
header('Location: index.php?logout=true');
exit();
?>
