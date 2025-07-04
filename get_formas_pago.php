<?php require_once 'proteger_api.php';
header('Content-Type: application/json');
require_once 'conexion.php';

try {
    $stmt = $conexion->query("SELECT id_forma_pago, nombre FROM formas_pago ORDER BY nombre");
    $formas_pago = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $formas_pago]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al obtener formas de pago.']);
}
?>
