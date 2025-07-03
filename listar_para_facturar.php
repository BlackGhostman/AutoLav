<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ['success' => false, 'data' => []];

// Consulta para traer los servicios que están "Para Facturar" y calcular su total
// Ordenado por cliente para facilitar la agrupación en el frontend
$sql = "
    SELECT 
        s.id_servicio,
        v.placa,
        v.tipo_vehiculo,
        c.id_cliente,
        c.nombre as nombre_cliente,
        (SELECT SUM(sd.precio_cobrado) FROM servicios_detalle sd WHERE sd.id_servicio = s.id_servicio) as total_servicio
    FROM servicios s
    JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo
    LEFT JOIN clientes c ON s.id_cliente = c.id_cliente
    JOIN estados e ON s.id_estado = e.id_estado
    WHERE e.nombre_estado = 'Para Facturar'
    ORDER BY c.id_cliente, s.hora_final ASC;
";

try {
    $stmt = $conexion->query($sql);
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response['success'] = true;
    $response['data'] = $servicios;

} catch (PDOException $e) {
    $response['message'] = 'Error al consultar los servicios para facturar: ' . $e->getMessage();
    http_response_code(500);
}

// Cerrar la conexión y enviar la respuesta
$conexion = null;
echo json_encode($response, JSON_PRETTY_PRINT);
?>
