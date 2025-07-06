<?php
require_once 'proteger_api.php';
header('Content-Type: application/json');
include 'conexion.php';

$response = ['success' => false, 'data' => [], 'message' => 'No se encontraron facturas.'];

try {
    // 1. Traer todas las facturas y sus estados.
    $sql = "SELECT f.id_factura, f.fecha_factura, f.cliente_cedula, f.cliente_email, f.total_pagado, f.es_electronica, f.estado_electronica, c.telefono AS cliente_telefono
            FROM facturas f
            LEFT JOIN clientes c ON f.cliente_cedula = c.cedula
            ORDER BY f.fecha_factura DESC";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $todas_las_facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Filtrar en PHP, usando trim() para eliminar espacios en blanco invisibles.
    $facturas_pendientes = array_filter($todas_las_facturas, function($factura) {
        return $factura['es_electronica'] == 1 && trim($factura['estado_electronica']) == 'pendiente';
    });

    if (!empty($facturas_pendientes)) {
        $invoice_ids = array_column($facturas_pendientes, 'id_factura');
        $placeholders = implode(',', array_fill(0, count($invoice_ids), '?'));

        // 3. Obtener detalles solo para las facturas filtradas.
        $sql_details = "
            SELECT 
                fd.id_factura,
                cs.nombre_servicio,
                sd.precio_cobrado,
                v.placa
            FROM factura_detalles fd
            LEFT JOIN servicios s ON fd.id_servicio = s.id_servicio
            LEFT JOIN servicios_detalle sd ON s.id_servicio = sd.id_servicio
            LEFT JOIN precios_servicios ps ON sd.id_precio = ps.id_precio
            LEFT JOIN catalogo_servicios cs ON ps.id_catalogo = cs.id_catalogo
            LEFT JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo
            WHERE fd.id_factura IN ($placeholders)
        ";
        
        $stmt_details = $conexion->prepare($sql_details);
        $stmt_details->execute($invoice_ids);
        $detalles_flat = $stmt_details->fetchAll(PDO::FETCH_ASSOC);
        
        $detalles_by_factura = [];
        foreach ($detalles_flat as $detalle) {
            $detalles_by_factura[$detalle['id_factura']][] = $detalle;
        }
        
        // 4. Adjuntar detalles y preparar la respuesta final.
        $facturas_finales = [];
        foreach ($facturas_pendientes as $factura) {
            $factura['detalles'] = $detalles_by_factura[$factura['id_factura']] ?? [];
            $facturas_finales[] = $factura;
        }

        $response['success'] = true;
        $response['data'] = array_values($facturas_finales); // Re-indexar para JSON
        $response['message'] = 'Facturas encontradas.';
    } else {
        $response['success'] = true;
        $response['data'] = [];
        $response['message'] = 'No hay facturas electrónicas pendientes.';
    }

} catch (Exception $e) {
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    error_log("Error en listar_facturas_electronicas_pendientes.php: " . $e->getMessage());
}

echo json_encode($response);
?>
