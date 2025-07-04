<?php require_once 'proteger_api.php';
header('Content-Type: application/json');
require_once 'conexion.php';

$id_factura = trim($_GET['id_factura'] ?? '');
$placa = trim($_GET['placa'] ?? '');
$nombre = trim($_GET['nombre'] ?? '');
$cedula = trim($_GET['cedula'] ?? '');

if (empty($id_factura) && empty($placa) && empty($nombre) && empty($cedula)) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

try {
    $query = "
        SELECT DISTINCT
            f.id_factura,
            f.fecha_factura,
            c.nombre AS nombre_cliente,
            (
                SELECT GROUP_CONCAT(DISTINCT v_inner.placa SEPARATOR ', ')
                FROM factura_detalles fd_inner
                JOIN servicios s_inner ON fd_inner.id_servicio = s_inner.id_servicio
                JOIN vehiculos v_inner ON s_inner.id_vehiculo = v_inner.id_vehiculo
                WHERE fd_inner.id_factura = f.id_factura
            ) AS placa
        FROM facturas f
        LEFT JOIN factura_detalles fd ON f.id_factura = fd.id_factura
        LEFT JOIN servicios s ON fd.id_servicio = s.id_servicio
        LEFT JOIN clientes c ON s.id_cliente = c.id_cliente
        LEFT JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo
    ";

    $conditions = [];
    $params = [];

    if (!empty($id_factura)) {
        $conditions[] = 'f.id_factura = :id_factura';
        $params[':id_factura'] = $id_factura;
    }
    if (!empty($placa)) {
        $conditions[] = 'UPPER(v.placa) LIKE :placa';
        $params[':placa'] = '%' . strtoupper($placa) . '%';
    }
    if (!empty($nombre)) {
        $conditions[] = 'UPPER(c.nombre) LIKE :nombre';
        $params[':nombre'] = '%' . strtoupper($nombre) . '%';
    }
    if (!empty($cedula)) {
        $conditions[] = 'UPPER(c.cedula) LIKE :cedula';
        $params[':cedula'] = '%' . strtoupper($cedula) . '%';
    }

    if (count($conditions) > 0) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $query .= ' ORDER BY f.id_factura DESC LIMIT 20';

    $stmt = $conexion->prepare($query);
    $stmt->execute($params);
    $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $facturas]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()]);
}
?>
