<?php
// data_dashboard.php

require_once 'conexion.php'; // Asegura que $conexion esté disponible

// --- FUNCIONES PARA OBTENER DATOS ---

/**
 * Obtiene los datos para el dashboard del Administrador.
 */
function get_admin_data($pdo) {
    $data = [];

    // Total de servicios hoy (estado 1: pendiente, 2: en proceso)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM servicios WHERE DATE(hora_inicio) = CURDATE()");
    $stmt->execute();
    $data['servicios_hoy'] = $stmt->fetchColumn();

    // Ingresos totales del día (basado en la fecha de la factura)
    $stmt = $pdo->prepare("SELECT SUM(total_pagado) FROM facturas WHERE DATE(fecha_factura) = CURDATE()");
    $stmt->execute();
    $data['ingresos_hoy'] = $stmt->fetchColumn() ?? 0;
    
    // Servicios en proceso (estado 2)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM servicios WHERE id_estado = 2");
    $stmt->execute();
    $data['servicios_proceso'] = $stmt->fetchColumn();

    return $data;
}

/**
 * Obtiene los datos para el dashboard de Caja.
 */
function get_caja_data($pdo) {
    $data = [];

    // Servicios finalizados pendientes de pago (estado 3)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM servicios WHERE id_estado = 3");
    $stmt->execute();
    $data['servicios_por_facturar'] = $stmt->fetchColumn();

    // Caja del día (total de facturas emitidas hoy)
    $stmt = $pdo->prepare("SELECT SUM(total_pagado) FROM facturas WHERE DATE(fecha_factura) = CURDATE()");
    $stmt->execute();
    $data['caja_dia'] = $stmt->fetchColumn() ?? 0;

    return $data;
}

/**
 * Obtiene los datos para el dashboard de Lavado.
 */
function get_lavado_data($pdo) {
    $data = [];

    // Vehículos en proceso (estado 2)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM servicios WHERE id_estado = 2");
    $stmt->execute();
    $data['vehiculos_en_proceso'] = $stmt->fetchColumn();

    // Servicios completados hoy (estado 3 o 4, finalizados hoy)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM servicios WHERE id_estado IN (3, 4) AND DATE(hora_final) = CURDATE()");
    $stmt->execute();
    $data['servicios_completados_hoy'] = $stmt->fetchColumn();

    return $data;
}

/**
 * Obtiene los datos para el gráfico de rendimiento semanal del Admin.
 */
function get_caja_chart_data($pdo) {
    $query = "
        SELECT 
            DATE(fecha_factura) as dia,
            SUM(total_pagado) as total_dia
        FROM facturas
        WHERE fecha_factura >= CURDATE() - INTERVAL 6 DAY
        GROUP BY DATE(fecha_factura)
        ORDER BY dia ASC;
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = array_fill(0, 7, 0);

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('D, j M', strtotime($date));
        foreach ($results as $row) {
            if ($row['dia'] == $date) {
                $data[6 - $i] = (float)$row['total_dia'];
            }
        }
    }

    return [
        'labels' => $labels,
        'datasets' => [['label' => 'Flujo de Caja','data' => $data,'backgroundColor' => 'rgba(54, 162, 235, 0.5)','borderColor' => 'rgba(54, 162, 235, 1)','borderWidth' => 1]]
    ];
}

function get_lavado_chart_data($pdo) {
    $query = "
        SELECT e.nombre_estado as estado, COUNT(s.id_servicio) as total
        FROM servicios s
        JOIN estados e ON s.id_estado = e.id_estado
        WHERE s.id_estado IN (1, 2, 3) -- Pendiente, En Proceso, Finalizado
        GROUP BY e.nombre_estado;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = array_column($results, 'estado');
    $data = array_column($results, 'total');

    return [
        'labels' => $labels,
        'datasets' => [['label' => 'Estado de Servicios','data' => $data,'backgroundColor' => ['rgba(255, 206, 86, 0.5)','rgba(54, 162, 235, 0.5)','rgba(75, 192, 192, 0.5)']]]
    ];
}

function get_admin_chart_data($pdo) {
    $query = "
        SELECT 
            DATE(fecha_factura) as dia,
            SUM(total_pagado) as ingresos,
            COUNT(id_factura) as servicios
        FROM facturas
        WHERE fecha_factura >= CURDATE() - INTERVAL 6 DAY
        GROUP BY DATE(fecha_factura)
        ORDER BY dia ASC;
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inicializar arrays para los últimos 7 días
    $labels = [];
    $ingresos_data = array_fill(0, 7, 0);
    $servicios_data = array_fill(0, 7, 0);

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('D, j M', strtotime($date)); // Formato: 'Lun, 1 Jul'

        // Usar un índice para el array de datos
        $data_index = 6 - $i;

        foreach ($results as $row) {
            if ($row['dia'] == $date) {
                $ingresos_data[$data_index] = (float)$row['ingresos'];
                $servicios_data[$data_index] = (int)$row['servicios'];
            }
        }
    }

    return [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Ingresos',
                'data' => $ingresos_data,
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'yAxisID' => 'y-ingresos',
                'tension' => 0.1
            ],
            [
                'label' => 'Servicios',
                'data' => $servicios_data,
                'borderColor' => 'rgba(255, 159, 64, 1)',
                'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                'yAxisID' => 'y-servicios',
                'tension' => 0.1
            ]
        ]
    ];
}
