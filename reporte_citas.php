<?php require_once 'proteger_api.php';
header('Content-Type: application/json');
require_once 'conexion.php';

$startDate = $_GET['start'] ?? '';
$endDate = $_GET['end'] ?? '';

if (empty($startDate) || empty($endDate)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Fechas de inicio y fin son requeridas.']);
    exit;
}

$endDate = $endDate . ' 23:59:59';

try {
    // Consulta para el resumen de estados
    $query_summary = "
        SELECT 
            estado,
            COUNT(id_cita) AS cantidad
        FROM citas
        WHERE fecha_cita BETWEEN :startDate AND :endDate
        GROUP BY estado
    ";
    $stmt_summary = $conexion->prepare($query_summary);
    $stmt_summary->execute([':startDate' => $startDate, ':endDate' => $endDate]);
    $summary_raw = $stmt_summary->fetchAll(PDO::FETCH_ASSOC);

    // Formatear resumen
    $summary = [
        'Total' => 0,
        'Pendiente' => 0,
        'Confirmada' => 0,
        'Completada' => 0,
        'Cancelada' => 0
    ];
    foreach ($summary_raw as $row) {
        if (isset($summary[$row['estado']])) {
            $summary[$row['estado']] = (int)$row['cantidad'];
        }
        $summary['Total'] += (int)$row['cantidad'];
    }

    // Consulta para los detalles
    $query_details = "
        SELECT 
            id_cita,
            fecha_cita,
            estado,
            nombre_cliente,
            placa
        FROM citas
        WHERE fecha_cita BETWEEN :startDate AND :endDate
        ORDER BY fecha_cita DESC
    ";
    $stmt_details = $conexion->prepare($query_details);
    $stmt_details->execute([':startDate' => $startDate, ':endDate' => $endDate]);
    $details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => ['summary' => $summary, 'details' => $details]]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
