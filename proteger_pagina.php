<?php
// Iniciar la sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión.
// Si no existe el user_id en la sesión, significa que no está logueado.
if (!isset($_SESSION['user_id'])) {
    // Guardar un mensaje para el usuario (opcional)
    $_SESSION['error_message'] = 'Debes iniciar sesión para acceder a esta página.';
    
    // Redirigir al login
    header('Location: index.php');
    
    // Detener la ejecución del script actual
    exit();
}
?>
