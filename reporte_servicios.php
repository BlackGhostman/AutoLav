<?php
require_once 'proteger_api.php';
header('Content-Type: application/json');
require_once 'conexion.php';

$startDate = $_GET['start'] ?? '';
$endDate = $_GET['end'] ?? '';

if (empty($startDate) || empty($endDate)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Fechas de inicio y fin son requeridas.']);
    exit;
}

// Asegurarse de que la fecha final incluya todo el día
$endDate = $endDate . ' 23:59:59';

try {
    $query = "
        SELECT 
            v.placa,
            v.tipo_vehiculo,
            cs.nombre_servicio,
            s.hora_inicio,
            s.hora_final,
            TIMESTAMPDIFF(MINUTE, s.hora_inicio, s.hora_final) AS duracion_minutos
        FROM servicios s
        JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo
        LEFT JOIN servicios_detalle sd ON s.id_servicio = sd.id_servicio
        LEFT JOIN precios_servicios ps ON sd.id_precio = ps.id_precio
        LEFT JOIN catalogo_servicios cs ON ps.id_catalogo = cs.id_catalogo
        WHERE s.hora_inicio BETWEEN :startDate AND :endDate
        ORDER BY s.hora_inicio DESC
    ";

    $stmt = $conexion->prepare($query);
    $stmt->execute([':startDate' => $startDate, ':endDate' => $endDate]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $services]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
