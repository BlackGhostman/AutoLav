<?php
header('Content-Type: application/json');

require_once 'conexion.php';

// --- OBTENER PARÁMETROS DE LA URL ---
$fecha_filtro = isset($_GET['fecha']) ? $_GET['fecha'] : null;
$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : null;

// --- CONSTRUIR LA CONSULTA SQL ---
$sql = "SELECT 
            id_cita, 
            placa, 
            cedula_cliente, 
            nombre_cliente, 
            telefono_cliente, 
            email_cliente, 
            fecha_cita, 
            hora_cita, 
            tipo_vehiculo, 
            servicio_base, 
            servicios_extras, 
            estado
        FROM citas WHERE 1=1";

$params = [];

if ($fecha_filtro === 'hoy') {
    $sql .= " AND fecha_cita = CURDATE()";
} elseif (!empty($fecha_filtro)) {
    $sql .= " AND fecha_cita = ?";
    $params[] = $fecha_filtro;
}

if (!empty($estado_filtro)) {
    $sql .= " AND estado = ?";
    $params[] = $estado_filtro;
}

$sql .= " ORDER BY hora_cita ASC";

// --- PREPARAR Y EJECUTAR LA CONSULTA CON PDO ---
try {
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decodificar el campo JSON 'servicios_extras' para cada cita
    foreach ($citas as &$cita) {
        $extras = json_decode($cita['servicios_extras'], true);
        // Si json_decode falla o el resultado no es un array, se asigna un array vacío.
        $cita['servicios_extras'] = is_array($extras) ? $extras : [];
    }

    // Devolver los resultados como JSON
    echo json_encode($citas);

} catch (PDOException $e) {
    // Manejar errores de la base de datos
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);

} finally {
    // Asegurarse de que la conexión siempre se cierre
    $conexion = null;
}
?>
