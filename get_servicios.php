<?php require_once 'proteger_api.php';
// Establece la cabecera para devolver JSON
header('Content-Type: application/json; charset=utf-8');
// Incluye la conexión a la base de datos
require 'conexion.php'; // Provee la variable $conexion (PDO)

// Obtiene el parámetro opcional 'vehicle_type' de la URL
$vehicle_type_filter = isset($_GET['vehicle_type']) ? $_GET['vehicle_type'] : null;

try {
    $pdo = $conexion;

    // Si se especifica un tipo de vehículo, devuelve solo sus lavados base.
    if ($vehicle_type_filter) {
        $response = ['baseServices' => []];
        $sql = "SELECT 
                    cs.id_catalogo,
                    cs.nombre_servicio, 
                    ps.precio 
                 FROM precios_servicios ps
                 JOIN catalogo_servicios cs ON ps.id_catalogo = cs.id_catalogo
                 WHERE cs.categoria = 'Lavado Principal' AND ps.tipo_vehiculo = :vehicle_type
                 ORDER BY cs.id_catalogo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':vehicle_type' => $vehicle_type_filter]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $response['baseServices'][] = [
                'id' => (int)$row['id_catalogo'],
                'name' => $row['nombre_servicio'],
                'price' => (float)$row['precio']
            ];
        }
    } else {
        // Si NO se especifica un tipo, devuelve los datos iniciales:
        // la lista de tipos de vehículo y los servicios adicionales.
        $response = [
            'vehicleTypes' => [],
            'extraServices' => []
        ];

        // Obtiene una lista única de tipos de vehículo que tienen servicios de lavado
        $sql_types = "SELECT DISTINCT tipo_vehiculo FROM precios_servicios WHERE id_catalogo IN (SELECT id_catalogo FROM catalogo_servicios WHERE categoria = 'Lavado Principal') ORDER BY tipo_vehiculo";
        $stmt_types = $pdo->query($sql_types);
        while ($row = $stmt_types->fetch(PDO::FETCH_ASSOC)) {
            $response['vehicleTypes'][] = $row['tipo_vehiculo'];
        }

        // Obtiene todos los servicios adicionales
        $sql_extra = "SELECT 
                        cs.id_catalogo,
                        cs.nombre_servicio, 
                        ps.precio 
                      FROM catalogo_servicios cs
                      JOIN precios_servicios ps ON cs.id_catalogo = ps.id_catalogo
                      WHERE cs.categoria != 'Lavado Principal'
                      GROUP BY cs.id_catalogo, cs.nombre_servicio
                      ORDER BY cs.id_catalogo";
        $stmt_extra = $pdo->query($sql_extra);
        while ($row = $stmt_extra->fetch(PDO::FETCH_ASSOC)) {
            $response['extraServices'][] = [
                'id' => (int)$row['id_catalogo'],
                'name' => $row['nombre_servicio'],
                'price' => (float)$row['precio']
            ];
        }
    }

} catch (PDOException $e) {
    // Manejo de errores de la base de datos
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
    exit;
}

// Devuelve la respuesta final en formato JSON
echo json_encode($response);
?>
