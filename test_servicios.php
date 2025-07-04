<?php require_once 'proteger_pagina.php';
// --------------------------------------------------------
// Archivo de Prueba para Obtener Servicios del Catálogo
// --------------------------------------------------------
// Este script se conecta a la base de datos, consulta la tabla
// `catalogo_servicios` y devuelve los resultados en formato JSON.
// Es útil para verificar la conexión y la consulta de datos.
// --------------------------------------------------------

// Establecer la cabecera para devolver una respuesta en formato JSON.
// Esto es estándar para crear APIs o endpoints de datos.
header('Content-Type: application/json');

// Incluir el archivo de conexión. Si la conexión falla,
// el script 'conexion.php' se encargará de detener la ejecución y mostrar un error.
require_once 'conexion.php';

// Preparar la respuesta por defecto.
$response = [
    'success' => false,
    'message' => 'No se pudo procesar la solicitud.',
    'data' => []
];

// Definir la consulta SQL para obtener todos los servicios del catálogo.
$sql = "SELECT id_catalogo, nombre_servicio, categoria FROM catalogo_servicios ORDER BY categoria, nombre_servicio;";

// Ejecutar la consulta.
$result = $conn->query($sql);

// Verificar si la consulta se ejecutó correctamente y si devolvió resultados.
if ($result) {
    if ($result->num_rows > 0) {
        // Si hay resultados, procesarlos.
        $servicios = [];
        // fetch_assoc() obtiene una fila de resultados como un array asociativo.
        while($row = $result->fetch_assoc()) {
            // Añadir cada fila (servicio) al array de servicios.
            $servicios[] = $row;
        }
        
        // Actualizar la respuesta para indicar éxito y adjuntar los datos.
        $response['success'] = true;
        $response['message'] = 'Servicios recuperados exitosamente.';
        $response['data'] = $servicios;

    } else {
        // La consulta fue exitosa, pero no se encontraron filas.
        $response['success'] = true;
        $response['message'] = 'No se encontraron servicios en el catálogo.';
    }
} else {
    // La consulta SQL falló.
    $response['message'] = 'Error al ejecutar la consulta SQL.';
    // Para depuración, podrías añadir el error específico:
    // $response['error_detail'] = $conn->error;
}

// Cerrar la conexión a la base de datos para liberar recursos.
$conn->close();

// Imprimir la respuesta final en formato JSON.
echo json_encode($response, JSON_PRETTY_PRINT);

?>
