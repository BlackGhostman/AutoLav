<?php
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
            COUNT(id_factura) AS numero_facturas,
            SUM(total_pagado) AS total_facturado,
            SUM(descuento) AS total_descuentos,
            AVG(total_pagado) AS promedio_factura
        FROM facturas
        WHERE fecha_factura BETWEEN :startDate AND :endDate
    ";

    $stmt = $conexion->prepare($query);
    $stmt->execute([':startDate' => $startDate, ':endDate' => $endDate]);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);

    $query_details = "
        SELECT 
            f.id_factura, 
            f.fecha_factura, 
            f.total_pagado, 
            c.nombre as nombre_cliente
        FROM facturas f
        LEFT JOIN (
            SELECT DISTINCT fd.id_factura, s.id_cliente
            FROM factura_detalles fd
            JOIN servicios s ON fd.id_servicio = s.id_servicio
        ) AS detalles ON f.id_factura = detalles.id_factura
        LEFT JOIN clientes c ON detalles.id_cliente = c.id_cliente
        WHERE f.fecha_factura BETWEEN :startDate AND :endDate
        ORDER BY f.fecha_factura DESC
    ";

    $stmt_details = $conexion->prepare($query_details);
    $stmt_details->execute([':startDate' => $startDate, ':endDate' => $endDate]);
    $details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => ['summary' => $summary, 'details' => $details]]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
