<?php require_once 'proteger_api.php';
header('Content-Type: application/json');
require_once 'conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : null;

try {
    // La transacción se manejará dentro de cada caso que modifica datos (abrir, cerrar)
    // para no bloquear la lectura (get_resumen).
    
    switch ($accion) {
        case 'abrir':
            $conexion->beginTransaction();
            // 1. Verificar si ya hay una caja abierta
            $stmt_check = $conexion->prepare("SELECT COUNT(*) FROM caja WHERE fecha_cierre IS NULL");
            $stmt_check->execute();
            if ($stmt_check->fetchColumn() > 0) {
                throw new Exception("Ya existe una sesión de caja abierta. Por favor, ciérrela antes de abrir una nueva.");
            }

            // 2. Abrir nueva caja
            $monto_inicial = isset($_POST['monto_inicial']) ? floatval($_POST['monto_inicial']) : 0;
            
            $sql = "INSERT INTO caja (monto_inicial, fecha_apertura) VALUES (?, NOW())";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$monto_inicial]);
            $conexion->commit();
            
            echo json_encode(['success' => true, 'message' => 'Caja abierta correctamente.']);
            break;

        case 'get_resumen':
            // 1. Obtener la caja abierta actual
            $stmt = $conexion->prepare("SELECT * FROM caja WHERE fecha_cierre IS NULL ORDER BY fecha_apertura DESC LIMIT 1");
            $stmt->execute();
            $caja_abierta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$caja_abierta) {
                // No hay caja abierta, devolver un estado que el frontend pueda interpretar
                echo json_encode(['success' => true, 'data' => ['caja_abierta' => false]]);
                break; // Salir del switch
            }

            // 2. Calcular ventas desde la apertura
            $fecha_apertura = $caja_abierta['fecha_apertura'];
            $sql_ventas = "SELECT SUM(f.total_pagado) as total_ventas, fp.categoria_caja, COUNT(f.id_factura) as total_servicios FROM facturas f JOIN formas_pago fp ON f.id_forma_pago = fp.id_forma_pago WHERE f.fecha_factura >= ? GROUP BY fp.categoria_caja";
            $stmt_ventas = $conexion->prepare($sql_ventas);
            $stmt_ventas->execute([$fecha_apertura]);
            $ventas = $stmt_ventas->fetchAll(PDO::FETCH_ASSOC);

            $total_ventas_efectivo = 0;
            $total_ventas_tarjeta = 0;
            $total_servicios = 0;

            foreach ($ventas as $venta) {
                if ($venta['categoria_caja'] == 'Efectivo') {
                    $total_ventas_efectivo += $venta['total_ventas'];
                }
                if ($venta['categoria_caja'] == 'Tarjeta') {
                    $total_ventas_tarjeta += $venta['total_ventas'];
                }
                $total_servicios += $venta['total_servicios'];
            }
            
            $monto_inicial = floatval($caja_abierta['monto_inicial']);
            $monto_esperado = $monto_inicial + $total_ventas_efectivo;

                        $resumen = [
                'caja_abierta' => true,
                'monto_inicial' => (float)$monto_inicial,
                'total_ventas_efectivo' => (float)$total_ventas_efectivo,
                'total_ventas_tarjeta' => (float)$total_ventas_tarjeta,
                'total_ventas_general' => (float)($total_ventas_efectivo + $total_ventas_tarjeta),
                'total_servicios' => (int)$total_servicios,
                'monto_esperado' => (float)$monto_esperado
            ];

            echo json_encode(['success' => true, 'data' => $resumen]);
            break;

        case 'cerrar':
            $conexion->beginTransaction();
            // 1. Obtener la caja abierta actual
            $stmt = $conexion->prepare("SELECT * FROM caja WHERE fecha_cierre IS NULL ORDER BY fecha_apertura DESC LIMIT 1");
            $stmt->execute();
            $caja_abierta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$caja_abierta) {
                throw new Exception("No hay ninguna caja abierta para cerrar.");
            }

            // 2. Recibir datos del POST
            $monto_real_en_caja = isset($_POST['monto_real_en_caja']) ? floatval($_POST['monto_real_en_caja']) : 0;
            $notas = isset($_POST['notas']) ? trim($_POST['notas']) : '';

            // 3. Re-calcular el total de ventas para asegurar consistencia
            $fecha_apertura = $caja_abierta['fecha_apertura'];
            $sql_ventas = "SELECT SUM(f.total_pagado) as total_ventas, fp.categoria_caja FROM facturas f JOIN formas_pago fp ON f.id_forma_pago = fp.id_forma_pago WHERE f.fecha_factura >= ? GROUP BY fp.categoria_caja";
            $stmt_ventas = $conexion->prepare($sql_ventas);
            $stmt_ventas->execute([$fecha_apertura]);
            $ventas = $stmt_ventas->fetchAll(PDO::FETCH_ASSOC);

            $total_ventas_efectivo = 0;
            $total_ventas_general = 0;
            foreach ($ventas as $venta) {
                if ($venta['categoria_caja'] == 'Efectivo') {
                    $total_ventas_efectivo += $venta['total_ventas'];
                }
                $total_ventas_general += $venta['total_ventas'];
            }

            // 4. Calcular diferencia
            $monto_inicial = floatval($caja_abierta['monto_inicial']);
            $monto_esperado = $monto_inicial + $total_ventas_efectivo;
            $diferencia = $monto_real_en_caja - $monto_esperado;

            // 5. Actualizar la base de datos
            $sql_update = "UPDATE caja SET 
                            fecha_cierre = NOW(), 
                            monto_final_efectivo = ?, 
                            total_ventas = ?, 
                            diferencia = ?, 
                            notas = ?
                          WHERE id_caja = ?";
            
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->execute([
                $monto_real_en_caja,
                $total_ventas_general,
                $diferencia,
                $notas,
                $caja_abierta['id_caja']
            ]);
            $conexion->commit();

            echo json_encode(['success' => true, 'message' => 'Caja cerrada correctamente.']);
            break;

        default:
            throw new Exception("Acción no válida.");
            break;
    }

} catch (Exception $e) {
    // Si algo falla y hay una transacción activa, revertirla
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
