<?php
// --------------------------------------------------------
// Archivo para Buscar un Cliente por Cédula
// --------------------------------------------------------
// Prioriza la búsqueda en la base de datos local.
// Si no encuentra al cliente, consulta el web service externo.
// --------------------------------------------------------

header('Content-Type: application/json');
require_once 'conexion.php'; // Incluye la conexión a la BD

$response = ['success' => false, 'message' => 'Cédula no proporcionada.', 'source' => 'local'];

if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];

    try {
        // 1. Buscar primero en la base de datos local usando PDO
        $stmt_local = $conexion->prepare("SELECT nombre, celular, email FROM clientes WHERE cedula = ?");
        $stmt_local->execute([$cedula]);
        $cliente_local = $stmt_local->fetch(PDO::FETCH_ASSOC);

        if ($cliente_local) {
            // Cliente encontrado en la BD local
            $response['success'] = true;
            $response['message'] = 'Cliente encontrado en la base de datos local.';
            $response['data'] = $cliente_local;
            $response['source'] = 'local';
        } else {
            // 2. Si no se encuentra, buscar en el Web Service externo
            $response['source'] = 'webservice';
            $ws_url = 'https://gestiones-administrativas.curridabat.go.cr/wspadron.asmx/traePersonaXCedula?cedula=' . urlencode($cedula);
            $context = stream_context_create(['http' => ['timeout' => 10]]);
            $xml_string = @file_get_contents($ws_url, false, $context);

            if ($xml_string) {
                try {
                    $xml = new SimpleXMLElement($xml_string);
                    $json_string = (string)$xml;
                    if (!empty($json_string) && $json_string !== '[]') {
                        $data = json_decode($json_string, true);
                        if (json_last_error() === JSON_ERROR_NONE && !empty($data)) {
                            $persona = $data[0];
                            $nombre_completo = trim($persona['Nombre'] . ' ' . $persona['Primer_Apellido'] . ' ' . $persona['Segundo_Apellido']);
                            
                            $response['success'] = true;
                            $response['message'] = 'Cliente encontrado via Web Service.';
                            $response['data'] = [
                                'nombre' => ucwords(strtolower($nombre_completo)),
                                'celular' => '', // Web service no provee estos datos
                                'email' => ''
                            ];
                        } else {
                            $response['message'] = 'Cédula no encontrada en el Web Service.';
                        }
                    } else {
                        $response['message'] = 'Cédula no encontrada en el Web Service.';
                    }
                } catch (Exception $e) {
                    $response['message'] = 'Error al procesar respuesta del Web Service.';
                }
            } else {
                $response['message'] = 'Error al conectar con el Web Service.';
            }
        }
    } catch (PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'Error de base de datos: ' . $e->getMessage();
        http_response_code(500);
    }
}

// Cerrar la conexión y enviar la respuesta
$conexion = null;
echo json_encode($response);
?>
