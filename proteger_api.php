<?php
// Iniciar o reanudar la sesión. Es crucial para verificar si el usuario está "logueado".
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si NO hay una sesión de usuario activa, entonces procedemos a validar el token de la API.
// Esto protege los endpoints de accesos externos no autenticados (como n8n).
// Las llamadas desde el propio frontend (con una sesión iniciada) no necesitarán el token.
if (!isset($_SESSION['user_id'])) { // Asumimos que 'user_id' se establece en el login.

    // Define el token de autenticación esperado.
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
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['message' => 'Authorization failed - please check your credentials']);
        exit();
    }
}

// Si hay una sesión de usuario o si el token es válido, la ejecución del script continuará.

?>
