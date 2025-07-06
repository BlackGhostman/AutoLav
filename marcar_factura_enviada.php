<?php
require_once 'proteger_api.php';
header('Content-Type: application/json');
include 'conexion.php';

$response = ['success' => false, 'message' => 'Solicitud no válida.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_factura'])) {
    $id_factura = intval($_POST['id_factura']);

    try {
        $sql = "UPDATE facturas 
                SET estado_electronica = 'enviada' 
                WHERE id_factura = ? AND es_electronica = 1";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_factura]);
        
        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Factura actualizada correctamente.';
        } else {
            $response['message'] = 'No se pudo actualizar la factura o ya estaba actualizada.';
        }
        
    } catch (Exception $e) {
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
        error_log("Error en marcar_factura_enviada.php: " . $e->getMessage());
    }
}

echo json_encode($response);
?>
