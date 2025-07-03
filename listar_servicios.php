<?php
header('Content-Type: application/json');
require_once 'conexion.php'; // Este archivo ya crea el objeto $conexion (PDO)

$response = [
    'success' => false,
    'message' => 'No se pudo procesar la solicitud.',
    'data' => []
];

try {
    // Consulta para traer los servicios que están "En Proceso"
    $sql = "
        SELECT 
            s.id_servicio,
            s.hora_inicio,
            v.placa,
            v.tipo_vehiculo,
            c.nombre as nombre_cliente
        FROM servicios s
        JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo
        LEFT JOIN clientes c ON s.id_cliente = c.id_cliente
        JOIN estados e ON s.id_estado = e.id_estado
        WHERE e.nombre_estado = 'En Proceso'
        ORDER BY s.hora_inicio ASC;
    ";

    // Usamos el objeto $conexion (PDO) y sus métodos
    $stmt = $conexion->query($sql);
    
    // PDO::FETCH_ASSOC ya está configurado por defecto en conexion.php
    $servicios = $stmt->fetchAll();

    $response['success'] = true;
    $response['message'] = 'Servicios en proceso recuperados.';
    $response['data'] = $servicios;

} catch (PDOException $e) {
    // Capturar cualquier error de la base de datos
    $response['message'] = 'Error al ejecutar la consulta: ' . $e->getMessage();
}

// Cerrar la conexión en PDO se hace asignando null
$conexion = null;

echo json_encode($response, JSON_PRETTY_PRINT);
?>
