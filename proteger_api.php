<?php
// Iniciar la sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión.
if (!isset($_SESSION['user_id'])) {
    // Establecer el tipo de contenido a JSON
    header('Content-Type: application/json');
    // Enviar un código de estado de no autorizado (401)
    http_response_code(401); 
    
    // Enviar un mensaje de error en JSON y detener la ejecución
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado. Se requiere iniciar sesión.']);
    exit();
}
?>
