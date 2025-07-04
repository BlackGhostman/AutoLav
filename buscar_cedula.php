<?php require_once 'proteger_api.php';
// Establecer la cabecera para devolver contenido en formato JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permite el acceso desde cualquier origen, ajustar en producción.

// Respuesta por defecto en caso de error
$response = [
    'success' => false,
    'message' => 'Cédula no proporcionada o inválida.',
    'nombreCompleto' => ''
];

// Verificar que el parámetro 'cedula' fue enviado
if (isset($_GET['cedula']) && strlen($_GET['cedula']) === 9) {
    $cedula = $_GET['cedula'];

    // URL del Web Service del padrón de Curridabat
    $ws_url = 'https://gestiones-administrativas.curridabat.go.cr/wspadron.asmx/traePersonaXCedula?cedula=' . urlencode($cedula);

    // Opciones para el stream context (para manejar errores y timeouts)
    $context_options = [
        'http' => [
            'method' => 'GET',
            'timeout' => 10, // Timeout de 10 segundos
        ],
    ];
    $context = stream_context_create($context_options);

    // Realizar la petición al Web Service. Usamos @ para suprimir warnings en caso de fallo.
    $xml_string = @file_get_contents($ws_url, false, $context);

    if ($xml_string === false) {
        $response['message'] = 'Error al conectar con el servicio web externo.';
    } else {
        try {
            // Cargar el XML recibido
            $xml = new SimpleXMLElement($xml_string);
            
            // El resultado real está como un string dentro de la etiqueta <string>
            // Este string interno tiene formato JSON
            $json_string = (string)$xml;

            if (empty($json_string) || $json_string === '[]') {
                 $response['message'] = 'La cédula no fue encontrada en el padrón.';
            } else {
                // Decodificar el JSON
                $data = json_decode($json_string, true);

                // Verificar si la decodificación fue exitosa y si hay datos
                if (json_last_error() === JSON_ERROR_NONE && !empty($data)) {
                    // El resultado es un array, tomamos el primer elemento
                    $persona = $data[0];

                    // Concatenar el nombre completo
                    $nombre_completo = trim($persona['Nombre'] . ' ' . $persona['Primer_Apellido'] . ' ' . $persona['Segundo_Apellido']);

                    $response['success'] = true;
                    $response['message'] = 'Nombre encontrado exitosamente.';
                    $response['nombreCompleto'] = ucwords(strtolower($nombre_completo)); // Formatear a tipo oración
                } else {
                    $response['message'] = 'El formato de respuesta del servicio no es válido.';
                }
            }
        } catch (Exception $e) {
            $response['message'] = 'Error al procesar la respuesta XML: ' . $e->getMessage();
        }
    }
}

// Devolver la respuesta final en formato JSON
echo json_encode($response);

?>
