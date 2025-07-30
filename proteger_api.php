<?php
// Define el token de autenticación esperado.
// IMPORTANTE: Puedes cambiar '6e5f4d3c2b1a6e5f4d3c2b1a6e5f4d3c' por tu propio token secreto.
define('API_SECRET_TOKEN', '6e5f4d3c2b1a6e5f4d3c2b1a6e5f4d3c');

// Función para obtener todos los encabezados (polyfill para Nginx, etc.)
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

// Función para obtener el token del encabezado de autorización
function get_bearer_token() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        // Se espera el formato 'Bearer <token>'
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

// Obtener el token enviado en la solicitud
$token = get_bearer_token();

// Verificar si el token es válido
if ($token !== API_SECRET_TOKEN) {
    // Establecer el tipo de contenido a JSON
    header('Content-Type: application/json');
    // Enviar un código de estado de no autorizado (401)
    http_response_code(401);
    
    // Enviar un mensaje de error en JSON y detener la ejecución
    // Usamos el mismo mensaje que n8n espera para mayor claridad.
    echo json_encode(['message' => 'Authorization failed - please check your credentials']);
    exit();
}

// Si el token es válido, la ejecución del script que incluye este archivo continuará.

?>
