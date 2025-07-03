<?php
header('Content-Type: application/json');
// Incluye la conexión, que nos da la variable $conexion (un objeto PDO).
require_once 'conexion.php';

$response = ['success' => false, 'data' => []];

try {
    // Usamos el objeto de conexión PDO.
    $pdo = $conexion;

    // La consulta SQL para traer los servicios que están "Para Facturar" O "En Proceso"
    $sql = "
        SELECT 
            s.id_servicio,
            v.placa,
            v.tipo_vehiculo,
            c.id_cliente,
            c.nombre as nombre_cliente,
            e.nombre_estado,
            (SELECT SUM(sd.precio_cobrado) FROM servicios_detalle sd WHERE sd.id_servicio = s.id_servicio) as total_servicio
        FROM servicios s
        JOIN vehiculos v ON s.id_vehiculo = v.id_vehiculo
        LEFT JOIN clientes c ON s.id_cliente = c.id_cliente
        JOIN estados e ON s.id_estado = e.id_estado
        WHERE e.nombre_estado IN ('Para Facturar', 'En Proceso')
        ORDER BY c.id_cliente, s.hora_inicio ASC;
    ";

    // Preparamos y ejecutamos la consulta usando PDO.
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Obtenemos todos los resultados en un array asociativo.
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['data'] = $servicios;

} catch (PDOException $e) {
    // Si ocurre un error en la base de datos, lo capturamos.
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    http_response_code(500); // Enviamos un código de error de servidor.
}

// Con PDO, no es necesario cerrar la conexión manualmente.
echo json_encode($response, JSON_PRETTY_PRINT);
?>
