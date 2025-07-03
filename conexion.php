<?php
// --------------------------------------------------------
// Archivo de Conexión a la Base de Datos (Versión PDO)
// --------------------------------------------------------

// Credenciales de la base de datos.
$servername = "srv1138.hstgr.io";
$database   = "u876327316_auto";
$username   = "u876327316_auto";
$password   = "Peregrino21$";
$charset    = "utf8mb4";

// Data Source Name (DSN)
$dsn = "mysql:host=$servername;dbname=$database;charset=$charset";

// Opciones de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devolver arrays asociativos por defecto
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar preparaciones nativas
];

try {
    // Crear una nueva instancia de PDO.
    // La variable se llama $conexion para ser compatible con los otros scripts.
    $conexion = new PDO($dsn, $username, $password, $options);

} catch (PDOException $e) {
    // Si la conexión falla, se captura la excepción aquí.
    header('Content-Type: application/json');
    http_response_code(500); // Error interno del servidor
    
    echo json_encode([
        'success' => false,
        'message' => 'Error crítico de conexión a la base de datos.',
        // En modo de depuración, se podría mostrar el error real:
        // 'error_detail' => $e->getMessage()
    ]);
    
    // Detener la ejecución del script para prevenir más errores.
    exit();
}

// Si el script llega hasta aquí, la variable $conexion está lista para ser usada.
?>
