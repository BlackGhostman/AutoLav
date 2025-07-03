<?php
header('Content-Type: application/json');
// Incluye la conexión. Esto nos dará la variable $conexion con el objeto PDO.
require_once 'conexion.php'; 

$data = json_decode(file_get_contents('php://input'), true);
$response = ['success' => false, 'message' => 'ID de servicio no proporcionado.'];

if (isset($data['id_servicio'])) {
    $id_servicio = $data['id_servicio'];
    // Usamos la variable $conexion que viene del archivo incluido.
    $pdo = $conexion; 

    try {
        // Iniciamos una transacción para asegurar la integridad de los datos.
        $pdo->beginTransaction();

        // 1. Buscamos el ID del estado "Para Facturar" usando PDO.
        $stmt_estado = $pdo->prepare("SELECT id_estado FROM estados WHERE nombre_estado = ?");
        $stmt_estado->execute(['Para Facturar']);
        $id_estado_facturar = $stmt_estado->fetchColumn();

        if ($id_estado_facturar) {
            // 2. Preparamos la actualización del servicio.
            $stmt_update = $pdo->prepare("UPDATE servicios SET hora_final = NOW(), id_estado = ? WHERE id_servicio = ?");
            
            // Ejecutamos la consulta con los parámetros.
            $stmt_update->execute([$id_estado_facturar, $id_servicio]);

            // rowCount() nos dice cuántas filas fueron afectadas.
            if ($stmt_update->rowCount() > 0) {
                $response['success'] = true;
                $response['message'] = 'Servicio finalizado y listo para facturar.';
            } else {
                $response['message'] = 'No se encontró el servicio o ya estaba finalizado.';
            }
        } else {
            // Si no encontramos el estado, lanzamos un error para detener la transacción.
            throw new Exception('El estado "Para Facturar" no se encontró en la base de datos.');
        }

        // Si todo salió bien, confirmamos los cambios en la base de datos.
        $pdo->commit();

    } catch (Exception $e) {
        // Si algo falló, revertimos todos los cambios.
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $response['message'] = 'Error al actualizar el servicio: ' . $e->getMessage();
    }
}

// Con PDO, la conexión se cierra automáticamente cuando el script termina.
// No es necesario llamar a $conexion->close();
echo json_encode($response);
?>
