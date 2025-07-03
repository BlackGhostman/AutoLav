<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = [
    'success' => false,
    'message' => 'No se pudo procesar la solicitud.',
    'data' => []
];

// Consulta para traer los servicios que están "En Proceso" o "Para Facturar"
$sql = "
    SELECT 
        s.id_servicio,
        s.hora_inicio,
        v.placa,
        v.tipo_vehiculo,
        c.nombre as nombre_cliente,
        e.nombre_estado
    FROM servicios s
    JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo
    LEFT JOIN clientes c ON s.id_cliente = c.id_cliente
    JOIN estados e ON s.id_estado = e.id_estado
    WHERE e.nombre_estado IN ('En Proceso', 'Para Facturar')
    ORDER BY s.hora_inicio ASC;
";

try {
    // Usamos el objeto $conexion (PDO) que viene de 'conexion.php'
    $stmt = $conexion->query($sql);
    
    // Obtenemos todos los resultados
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['message'] = 'Servicios activos recuperados.';
    $response['data'] = $servicios;

} catch (PDOException $e) {
    // Si hay un error, lo capturamos y lo incluimos en la respuesta
    $response['message'] = 'Error al ejecutar la consulta: ' . $e->getMessage();
    http_response_code(500); // Enviar código de error del servidor
}

// Con PDO, la conexión se cierra asignando null a la variable
$conexion = null;

echo json_encode($response, JSON_PRETTY_PRINT);
?>
