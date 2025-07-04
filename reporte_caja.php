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

$endDate = $endDate . ' 23:59:59';

try {
    // --- Asunción de la estructura de la tabla ---
    // Se asume una tabla `caja_movimientos` con las columnas:
    // `fecha`, `tipo_movimiento` ('APERTURA', 'INGRESO', 'EGRESO', 'CIERRE'), `monto`.

    // Consulta para el resumen
    $query_summary = "
        SELECT 
            SUM(CASE WHEN tipo_movimiento = 'INGRESO' THEN monto ELSE 0 END) AS total_ingresos,
            SUM(CASE WHEN tipo_movimiento = 'EGRESO' THEN monto ELSE 0 END) AS total_egresos,
            (SELECT monto FROM caja_movimientos WHERE tipo_movimiento = 'APERTURA' AND fecha >= :startDate ORDER BY fecha ASC LIMIT 1) AS saldo_inicial
        FROM caja_movimientos
        WHERE fecha BETWEEN :startDate AND :endDate
    ";
    $stmt_summary = $conexion->prepare($query_summary);
    $stmt_summary->execute([':startDate' => $startDate, ':endDate' => $endDate]);
    $summary = $stmt_summary->fetch(PDO::FETCH_ASSOC);

    // Calcular Saldo Final
    $saldo_inicial = $summary['saldo_inicial'] ?? 0;
    $total_ingresos = $summary['total_ingresos'] ?? 0;
    $total_egresos = $summary['total_egresos'] ?? 0;
    $summary['saldo_final'] = $saldo_inicial + $total_ingresos - $total_egresos;

    // Consulta para los detalles
    $query_details = "
        SELECT 
            fecha,
            tipo_movimiento,
            monto,
            descripcion
        FROM caja_movimientos
        WHERE fecha BETWEEN :startDate AND :endDate
        ORDER BY fecha DESC
    ";
    $stmt_details = $conexion->prepare($query_details);
    $stmt_details->execute([':startDate' => $startDate, ':endDate' => $endDate]);
    $details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => ['summary' => $summary, 'details' => $details]]);

} catch (Exception $e) {
    http_response_code(500);
    // Devolver un mensaje de error que incluye la suposición para facilitar la depuración
    echo json_encode([
        'success' => false, 
        'message' => 'Error en la base de datos. Verifique que la tabla `caja_movimientos` y sus columnas (`fecha`, `tipo_movimiento`, `monto`) existan. Error original: ' . $e->getMessage()
    ]);
}
