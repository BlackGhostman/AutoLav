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

// Añadir la hora para cubrir todo el día
$startDateFormatted = $startDate . ' 00:00:00';
$endDateFormatted = $endDate . ' 23:59:59';

try {
    // Consulta para el resumen de las sesiones de caja cerradas
    $query_summary = "
        SELECT 
            SUM(total_ventas) as total_ventas,
            SUM(monto_inicial) as total_monto_inicial,
            SUM(monto_final_real) as total_monto_final,
            SUM(diferencia) as total_diferencia,
            COUNT(*) as numero_sesiones
        FROM caja_sesiones
        WHERE fecha_cierre BETWEEN :startDate AND :endDate AND estado = 'CERRADA'
    ";
    $stmt_summary = $conexion->prepare($query_summary);
    $stmt_summary->execute([':startDate' => $startDateFormatted, ':endDate' => $endDateFormatted]);
    $summary = $stmt_summary->fetch(PDO::FETCH_ASSOC);

    // Consulta para los detalles de cada sesión de caja
    $query_details = "
        SELECT 
            id,
            fecha_apertura,
            fecha_cierre,
            monto_inicial,
            total_ventas,
            monto_final_calculado,
            monto_final_real,
            diferencia,
            notas_cierre
        FROM caja_sesiones
        WHERE fecha_cierre BETWEEN :startDate AND :endDate AND estado = 'CERRADA'
        ORDER BY fecha_cierre DESC
    ";
    $stmt_details = $conexion->prepare($query_details);
    $stmt_details->execute([':startDate' => $startDateFormatted, ':endDate' => $endDateFormatted]);
    $details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay datos, devolver un resumen con ceros pero exitoso
    if ($summary['numero_sesiones'] == 0) {
        $summary = [
            'total_ventas' => 0,
            'total_monto_inicial' => 0,
            'total_monto_final' => 0,
            'total_diferencia' => 0,
            'numero_sesiones' => 0
        ];
    }

    echo json_encode(['success' => true, 'data' => ['summary' => $summary, 'details' => $details]]);

} catch (Exception $e) {
    http_response_code(500);
    $errorMessage = 'Error en la base de datos. Verifique que la tabla `caja_sesiones` y sus columnas existan. Error original: ' . $e->getMessage();
    echo json_encode(['success' => false, 'message' => $errorMessage]);
}
