<?php require_once 'proteger_api.php';
header('Content-Type: application/json');
require 'conexion.php';

$placa = isset($_GET['placa']) ? trim(strtoupper($_GET['placa'])) : '';
$cedula = isset($_GET['cedula']) ? trim($_GET['cedula']) : '';

if (empty($placa) && empty($cedula)) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó placa ni cédula.']);
    exit;
}

$response = ['success' => false, 'data' => null];

try {
    $pdo = $conexion;
    
    if (!empty($placa)) {
        // Búsqueda por placa (lógica existente)
        $sql = "SELECT v.tipo_vehiculo, c.cedula AS cedula_cliente, c.nombre AS nombre_cliente, c.celular AS celular_cliente, c.email AS email_cliente 
                FROM servicios s
                JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo
                LEFT JOIN clientes c ON s.id_cliente = c.id_cliente
                WHERE v.placa = :placa 
                ORDER BY s.id_servicio DESC 
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':placa', $placa, PDO::PARAM_STR);
    } else { // Búsqueda por cédula
        $sql = "SELECT c.cedula AS cedula_cliente, c.nombre AS nombre_cliente, c.celular AS celular_cliente, c.email AS email_cliente 
                FROM servicios s
                JOIN clientes c ON s.id_cliente = c.id_cliente
                WHERE c.cedula = :cedula
                ORDER BY s.id_servicio DESC 
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        $response['success'] = true;
        $response['data'] = $data;
    } else {
        $response['message'] = !empty($placa) ? 'No se encontraron servicios para esta placa.' : 'No se encontraron clientes para esta cédula.';
    }

} catch (PDOException $e) {
    http_response_code(500);
    $response['success'] = false;
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
}

echo json_encode($response);
?>
