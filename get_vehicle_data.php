<?php
header('Content-Type: application/json');
require 'conexion.php';

$placa = isset($_GET['placa']) ? trim(strtoupper($_GET['placa'])) : '';

if (empty($placa)) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó la placa.']);
    exit;
}

$response = ['success' => false, 'data' => null];

try {
    $pdo = $conexion;
    
    $sql = "SELECT v.tipo_vehiculo, c.cedula AS cedula_cliente, c.nombre AS nombre_cliente, c.celular AS celular_cliente, c.email AS email_cliente 
            FROM servicios s
            JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo
            LEFT JOIN clientes c ON s.id_cliente = c.id_cliente
            WHERE v.placa = :placa 
            ORDER BY s.id_servicio DESC 
            LIMIT 1";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':placa', $placa, PDO::PARAM_STR);
    $stmt->execute();
    
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($vehiculo) {
        $response['success'] = true;
        $response['data'] = $vehiculo;
    } else {
        $response['success'] = false;
        $response['message'] = 'No se encontraron servicios anteriores para esta placa.';
    }

} catch (PDOException $e) {
    http_response_code(500);
    $response['success'] = false;
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
}

echo json_encode($response);
?>
