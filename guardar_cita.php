<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ['success' => false, 'message' => 'Error: No se recibieron datos.'];

// Obtener el cuerpo de la solicitud JSON y decodificarlo
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if ($data) {
    // Validar datos obligatorios
    if (empty($data['placa']) || empty($data['cliente_nombre']) || empty($data['fecha_cita']) || empty($data['hora_cita'])) {
        $response['message'] = 'Faltan datos obligatorios: Placa, Nombre, Fecha u Hora.';
    } else {
        // Asignar variables desde los datos recibidos
        $placa = $data['placa'];
        $cedula = $data['cliente_cedula'] ?? null;
        $nombre = $data['cliente_nombre'];
        $telefono = $data['cliente_telefono'] ?? null;
        $email = $data['cliente_email'] ?? null;
        $fecha_cita = $data['fecha_cita'];
        $hora_cita = $data['hora_cita'];
        $tipo_vehiculo = $data['tipo_vehiculo'] ?? null;
        $servicio_base = $data['tipo_lavado'] ?? null;
        $servicios_extras = json_encode($data['servicios_adicionales'] ?? []);
        $notas = $data['notas'] ?? null;

        try {
            // Usar la conexión PDO $conexion e iniciar una transacción
            $conexion->beginTransaction();

            // --- PASO 1: Mantener tablas maestras ---
            if (!empty($cedula)) {
                $stmt_find_cliente = $conexion->prepare("SELECT id_cliente FROM clientes WHERE cedula = ?");
                $stmt_find_cliente->execute([$cedula]);
                if ($stmt_find_cliente->fetch() === false) {
                    $stmt_insert_cliente = $conexion->prepare("INSERT INTO clientes (cedula, nombre, celular, email) VALUES (?, ?, ?, ?)");
                    $stmt_insert_cliente->execute([$cedula, $nombre, $telefono, $email]);
                }
            }

            $stmt_find_vehiculo = $conexion->prepare("SELECT id_vehiculo FROM vehiculos WHERE placa = ?");
            $stmt_find_vehiculo->execute([$placa]);
            if ($stmt_find_vehiculo->fetch() === false) {
                $stmt_insert_vehiculo = $conexion->prepare("INSERT INTO vehiculos (placa, tipo_vehiculo) VALUES (?, ?)");
                $stmt_insert_vehiculo->execute([$placa, $tipo_vehiculo]);
            }

            // --- PASO 2: Insertar la cita ---
            $sql_cita = "INSERT INTO citas (placa, cedula_cliente, nombre_cliente, telefono_cliente, email_cliente, fecha_cita, hora_cita, tipo_vehiculo, servicio_base, servicios_extras, notas, estado) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";
            
            $stmt_cita = $conexion->prepare($sql_cita);
            $stmt_cita->execute([
                $placa, $cedula, $nombre, $telefono, $email, 
                $fecha_cita, $hora_cita, $tipo_vehiculo, 
                $servicio_base, $servicios_extras, $notas
            ]);
            
            $id_cita = $conexion->lastInsertId();
            $conexion->commit();

            $response['success'] = true;
            $response['message'] = 'Cita guardada exitosamente.';
            $response['id_cita'] = $id_cita;

        } catch (PDOException $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            $response['success'] = false;
            $response['message'] = 'Error en la operación de base de datos: ' . $e->getMessage();
            http_response_code(500);
        }
    }
}

// Cerrar la conexión y enviar la respuesta
$conexion = null;
echo json_encode($response);
?>
