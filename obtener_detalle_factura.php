<?php require_once 'proteger_api.php';
header('Content-Type: application/json');
require 'conexion.php';

$response = ['success' => false, 'message' => 'Error desconocido.'];

try {
    if (!isset($_GET['id_factura'])) {
        throw new Exception('No se proporcionó el ID de la factura.');
    }

    $id_factura = intval($_GET['id_factura']);

    // 1. Obtener datos de la factura principal
    $stmt_factura = $conexion->prepare("SELECT * FROM facturas WHERE id_factura = ?");
    $stmt_factura->execute([$id_factura]);
    $factura = $stmt_factura->fetch(PDO::FETCH_ASSOC);

    if (!$factura) {
        throw new Exception('Factura no encontrada.');
    }

    // 2. Obtener los detalles de los servicios (nombres y precios)
    // La lógica correcta es: factura -> servicios -> servicios_detalle -> catalogo_servicios
    $stmt_detalles = $conexion->prepare("
        SELECT 
            cs.nombre_servicio, 
            sd.precio_cobrado
        FROM factura_detalles fd
        JOIN servicios s ON fd.id_servicio = s.id_servicio
        JOIN servicios_detalle sd ON s.id_servicio = sd.id_servicio
        JOIN precios_servicios ps ON sd.id_precio = ps.id_precio
        JOIN catalogo_servicios cs ON ps.id_catalogo = cs.id_catalogo
        WHERE fd.id_factura = ?
    ");
    $stmt_detalles->execute([$id_factura]);
    $detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

    // 3. Obtener los datos del cliente y vehículo(s)
    $stmt_vehiculos = $conexion->prepare("
        SELECT DISTINCT
            v.placa, 
            c.nombre AS nombre_cliente, 
            c.cedula,
            c.celular
        FROM factura_detalles fd
        JOIN servicios s ON fd.id_servicio = s.id_servicio
        JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo
        LEFT JOIN clientes c ON s.id_cliente = c.id_cliente
        WHERE fd.id_factura = ?
    ");
    $stmt_vehiculos->execute([$id_factura]);
    $vehiculos = $stmt_vehiculos->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['message'] = 'Datos de la factura obtenidos con éxito.';
    $response['data'] = [
        'factura' => $factura,
        'vehiculos' => $vehiculos,
        'detalles' => $detalles
    ];

} catch (PDOException $e) {
    http_response_code(500);
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    http_response_code(400);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

