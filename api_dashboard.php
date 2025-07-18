<?php
// api_dashboard.php

header('Content-Type: application/json');
require_once 'data_dashboard.php'; // Reutilizamos las funciones y la conexión

$response = ['error' => 'No se especificó una acción válida.'];

if (isset($_GET['chart'])) {
    $chart_type = $_GET['chart'];

    switch ($chart_type) {
        case 'admin_weekly_performance':
            $response = get_admin_chart_data($conexion);
            break;
        case 'caja_flow':
            $response = get_caja_chart_data($conexion);
            break;
        case 'lavado_productivity':
            $response = get_lavado_chart_data($conexion);
            break;
        // Aquí se pueden agregar más casos para otros gráficos
        // case 'caja_flow':
        //     $response = get_caja_chart_data($conexion);
        //     break;
        // case 'lavado_productivity':
        //     $response = get_lavado_chart_data($conexion);
        //     break;
        default:
            $response = ['error' => 'Tipo de gráfico no reconocido.'];
            break;
    }
}

echo json_encode($response);
