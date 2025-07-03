<?php
// -------------------------------------------------------------------
// Archivo para Crear una Nueva Orden de Lavado (Convertido a PDO)
// -------------------------------------------------------------------
// Este script recibe datos JSON, los valida y los inserta en la BD
// usando transacciones PDO para garantizar la integridad de los datos.
// -------------------------------------------------------------------

header('Content-Type: application/json');
require_once 'conexion.php'; // Usa la conexión PDO ($conexion)

$data = json_decode(file_get_contents('php://input'), true);
$response = ['success' => false, 'message' => 'Datos inválidos o incompletos.'];

if (!isset($data['placa'], $data['vehicleType'], $data['baseWash'])) {
    echo json_encode($response);
    exit();
}

try {
    $conexion->beginTransaction();

    // --- PASO 1: Gestionar Cliente ---
    $id_cliente = null;
    if (!empty($data['cedula'])) {
        $stmt = $conexion->prepare("SELECT id_cliente FROM clientes WHERE cedula = ?");
        $stmt->execute([$data['cedula']]);
        $id_cliente = $stmt->fetchColumn();

        if (!$id_cliente) {
            $stmt = $conexion->prepare("INSERT INTO clientes (cedula, nombre, celular, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['cedula'], $data['nombre'], $data['celular'], $data['email']]);
            $id_cliente = $conexion->lastInsertId();
        }
    }

    // --- PASO 2: Gestionar Vehículo ---
    $stmt = $conexion->prepare("SELECT id_vehiculo FROM vehiculos WHERE placa = ?");
    $stmt->execute([$data['placa']]);
    $id_vehiculo = $stmt->fetchColumn();

    if (!$id_vehiculo) {
        $stmt = $conexion->prepare("INSERT INTO vehiculos (placa, tipo_vehiculo) VALUES (?, ?)");
        $stmt->execute([$data['placa'], $data['vehicleType']]);
        $id_vehiculo = $conexion->lastInsertId();
    }

    // --- PASO 3: Crear el Servicio Principal ---
    $hora_inicio_mysql = date("Y-m-d H:i:s", strtotime('today ' . $data['startTime']));
    $id_estado_en_proceso = 1; // ID para 'En Proceso'

    $stmt = $conexion->prepare("INSERT INTO servicios (id_vehiculo, id_cliente, hora_inicio, id_estado) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_vehiculo, $id_cliente, $hora_inicio_mysql, $id_estado_en_proceso]);
    $id_servicio = $conexion->lastInsertId();

    // --- PASO 3.5: Actualizar estado de la cita (si aplica) ---
    if (!empty($data['cita_id'])) {
        $stmt_update_cita = $conexion->prepare("UPDATE citas SET estado = 'Iniciado' WHERE id = ?");
        $stmt_update_cita->execute([$data['cita_id']]);
    }

    // --- PASO 4: Insertar los Detalles del Servicio ---
    $servicios_a_insertar = array_merge([$data['baseWash']], $data['extras'] ?? []);

    $stmt_find_price = $conexion->prepare("
        SELECT ps.id_precio FROM precios_servicios ps
        JOIN catalogo_servicios cs ON ps.id_catalogo = cs.id_catalogo
        WHERE cs.nombre_servicio = ? AND ps.tipo_vehiculo IN (?, 'General')
        ORDER BY FIELD(ps.tipo_vehiculo, ?, 'General')
        LIMIT 1
    ");

    $stmt_insert_detail = $conexion->prepare("INSERT INTO servicios_detalle (id_servicio, id_precio, precio_cobrado) VALUES (?, ?, ?)");

    foreach ($servicios_a_insertar as $servicio) {
        $nombre_servicio = $servicio['name'];
        $precio_cobrado = $servicio['price'];
        $tipo_vehiculo = $data['vehicleType'];

        $stmt_find_price->execute([$nombre_servicio, $tipo_vehiculo, $tipo_vehiculo]);
        $id_precio = $stmt_find_price->fetchColumn();

        if ($id_precio) {
            $stmt_insert_detail->execute([$id_servicio, $id_precio, $precio_cobrado]);
        } else {
            throw new Exception("No se encontró precio para '{$nombre_servicio}' en '{$tipo_vehiculo}' o 'General'.");
        }
    }

    $conexion->commit();
    $response = [
        'success' => true,
        'message' => "¡Servicio #{$id_servicio} iniciado exitosamente!",
        'id_servicio' => $id_servicio
    ];

} catch (Exception $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    $response['message'] = 'Error al crear la orden: ' . $e->getMessage();
}

// Devolver la respuesta final
echo json_encode($response);
?>
