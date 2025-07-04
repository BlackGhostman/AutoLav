<?php
// Siempre devolver JSON para que el frontend no falle.
header('Content-Type: application/json');

// Incluir la conexión a la base de datos (que usa PDO y define $conexion).
include 'conexion.php';

// Respuesta por defecto.
$response = [
    'success' => false,
    'message' => 'Solicitud no válida o método incorrecto.'
];

// --- Lógica Principal del Script ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Validación de datos de entrada.
    $missing_fields = [];
    if (!isset($_POST['id_servicio']) || !is_array($_POST['id_servicio']) || empty($_POST['id_servicio'])) {
        $missing_fields[] = 'id_servicio';
    }
    if (!isset($_POST['id_forma_pago']) || !is_numeric($_POST['id_forma_pago'])) {
        $missing_fields[] = 'id_forma_pago';
    }
    if (!isset($_POST['subtotal']) || !isset($_POST['descuento']) || !isset($_POST['total'])) {
        $missing_fields[] = 'montos (subtotal, descuento, total)';
    }

    if (!empty($missing_fields)) {
        $response['message'] = 'Datos incompletos. Faltan los siguientes campos: ' . implode(', ', $missing_fields);
        echo json_encode($response);
        exit;
    }

    // 2. Recolección y saneamiento de datos.
    $servicios_ids = $_POST['id_servicio']; // Es un array de IDs.
    $id_forma_pago = (int)$_POST['id_forma_pago'];
    $subtotal = floatval($_POST['subtotal']);
    $descuento = floatval($_POST['descuento']);
    $total_pagado = floatval($_POST['total']);

    // 3. Procesamiento con transacción PDO.
    try {
                // VERIFICAR SI HAY CAJA ABIERTA
        $stmt_caja_check = $conexion->prepare("SELECT COUNT(*) FROM caja WHERE fecha_cierre IS NULL");
        $stmt_caja_check->execute();
        if ($stmt_caja_check->fetchColumn() == 0) {
            throw new Exception("No se puede facturar porque no hay una caja abierta. Por favor, realice la apertura de caja.");
        }

        $conexion->beginTransaction();

        // Paso 1: Crear la factura principal en la tabla 'facturas'.
        $sql_factura = "INSERT INTO facturas (subtotal, descuento, total_pagado, id_forma_pago, fecha_factura) VALUES (?, ?, ?, ?, NOW())";
        $stmt_factura = $conexion->prepare($sql_factura);
        
        // El execute se hace con un array de los valores.
        $stmt_factura->execute([$subtotal, $descuento, $total_pagado, $id_forma_pago]);
        
        if ($stmt_factura->rowCount() <= 0) {
            throw new Exception("No se pudo crear la factura en la base de datos.");
        }
        
        // Obtener el ID de la factura recién creada.
        $id_factura = $conexion->lastInsertId();

        // Paso 2: Asociar cada servicio a la factura y actualizar su estado.
        $sql_detalle = "INSERT INTO factura_detalles (id_factura, id_servicio) VALUES (?, ?)";
        $stmt_detalle = $conexion->prepare($sql_detalle);

        $sql_update_servicio = "UPDATE servicios SET id_estado = 4 WHERE id_servicio = ?";
        $stmt_update_servicio = $conexion->prepare($sql_update_servicio);
        
        foreach ($servicios_ids as $id_servicio) {
            $id_servicio_int = intval($id_servicio);

            // Insertar en factura_detalles.
            $stmt_detalle->execute([$id_factura, $id_servicio_int]);
            if ($stmt_detalle->rowCount() <= 0) {
                throw new Exception("No se pudo registrar el detalle para el servicio ID: " . $id_servicio_int);
            }

            // Actualizar el estado en servicios.
            $stmt_update_servicio->execute([$id_servicio_int]);
            if ($stmt_update_servicio->rowCount() <= 0) {
                throw new Exception("No se pudo actualizar el estado para el servicio con ID: " . $id_servicio_int);
            }
        }

        // Si todo salió bien, confirmar la transacción.
        $conexion->commit();

        $response['success'] = true;
        $response['message'] = 'Factura generada correctamente.';
        $response['id_factura'] = $id_factura;

    } catch (Exception $e) {
        // Si algo falló, revertir todos los cambios.
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        $response['message'] = "Error en la transacción: " . $e->getMessage();
        error_log("Error en procesar_pago.php: " . $e->getMessage());
    }

    echo json_encode($response);
    exit;
}

// Si no es un método POST, devolver la respuesta por defecto.
echo json_encode($response);
?>