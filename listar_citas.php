<?php require_once 'proteger_api.php';
header('Content-Type: application/json');

require_once 'conexion.php';

// --- OBTENER PARÁMETROS DE LA URL ---
$fecha_filtro = $_GET['fecha'] ?? null;
$estado_filtro = $_GET['estado'] ?? null;

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
            servicios_extras 
        FROM citas WHERE 1=1";

$params = [];
$types = '';

if ($fecha_filtro === 'hoy') {
    $sql .= " AND fecha_cita = CURDATE()";
} elseif (!empty($fecha_filtro)) {
    $sql .= " AND fecha_cita = ?";
    $types .= 's';
    $params[] = $fecha_filtro;
}

if (!empty($estado_filtro)) {
    $sql .= " AND estado = ?";
    $types .= 's';
    $params[] = $estado_filtro;
}

$sql .= " ORDER BY hora_cita ASC";

$citas = [];

try {
    // --- PREPARAR Y EJECUTAR LA CONSULTA CON PDO ---
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);

    // --- PROCESAR RESULTADOS ---
    $citas_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($citas_raw as $row) {
        // Decodificar el campo servicios_extras para asegurar que sea un array.
        $extras = json_decode($row['servicios_extras'], true);
        $row['servicios_extras'] = is_array($extras) ? $extras : [];
        $citas[] = $row;
    }

} catch (PDOException $e) {
    http_response_code(500);
    // En un entorno de producción, es mejor no exponer detalles del error.
    // echo json_encode(['error' => 'Error en la base de datos.']);
    echo json_encode(['error' => 'Error al listar las citas: ' . $e->getMessage()]);
    $conexion = null; // Asegurarse de cerrar la conexión en caso de error
    exit();
}

// --- DEVOLVER RESULTADOS Y CERRAR CONEXIÓN ---
echo json_encode($citas);

$conexion = null;

