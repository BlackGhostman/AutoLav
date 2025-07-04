<?php
session_start();
require 'conexion.php';

header('Content-Type: application/json');

// Leer el cuerpo de la solicitud
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str);

if (!isset($json_obj->pin)) {
    echo json_encode(['success' => false, 'message' => 'PIN no proporcionado.']);
    exit();
}

$submitted_pin = $json_obj->pin;

try {
    // Como los PINs están hasheados, no podemos buscarlos directamente.
    // Obtenemos todos los usuarios y verificamos el PIN uno por uno.
    $stmt = $conexion->query('SELECT id, nombre, pin, rol FROM usuarios');
    $users = $stmt->fetchAll();

    $user_found = false;

    foreach ($users as $user) {
        // password_verify compara el pin enviado con el hash de la BD
        if (password_verify($submitted_pin, $user['pin'])) {
            // ¡PIN correcto! Creamos la sesión.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_role'] = $user['rol'];
            
            $user_found = true;
            break; // Salimos del bucle en cuanto encontramos al usuario
        }
    }

    if ($user_found) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'PIN incorrecto.']);
    }

} catch (PDOException $e) {
    // En un entorno de producción, registraríamos el error en lugar de mostrarlo.
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos.']);
}

?>
