<?php 
$currentPage = 'reportes';
include 'menu.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - AutoSpa Blue Line</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

<main id="main-content" class="lg:ml-64 p-6">

    <h1 class="text-3xl font-bold mb-6">Reportes</h1>

    <!-- Controles de Fecha -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="start-date" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                <input type="date" id="start-date" name="start-date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="end-date" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                <input type="date" id="end-date" name="end-date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="self-end">
                <button id="filter-btn" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">Generar Reporte</button>
            </div>
        </div>
    </div>

    <!-- Pestañas para los diferentes reportes -->
    <div class="mb-4 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="report-tabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="facturacion-tab" data-tabs-target="#facturacion" type="button" role="tab" aria-controls="facturacion" aria-selected="false">Facturación</button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="citas-tab" data-tabs-target="#citas" type="button" role="tab" aria-controls="citas" aria-selected="false">Citas</button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="caja-tab" data-tabs-target="#caja" type="button" role="tab" aria-controls="caja" aria-selected="false">Caja</button>
            </li>
        </ul>
    </div>

    <!-- Contenido de las Pestañas -->
    <div id="tab-content">
        <div class="hidden p-4 rounded-lg bg-white shadow-md" id="facturacion" role="tabpanel" aria-labelledby="facturacion-tab">
            <h2 class="text-xl font-bold mb-4">Reporte de Facturación</h2>
            <div id="facturacion-content">
                <p>Seleccione un rango de fechas y presione "Generar Reporte".</p>
            </div>
        </div>
        <div class="hidden p-4 rounded-lg bg-white shadow-md" id="citas" role="tabpanel" aria-labelledby="citas-tab">
            <h2 class="text-xl font-bold mb-4">Reporte de Citas</h2>
            <div id="citas-content">
                <p>Seleccione un rango de fechas y presione "Generar Reporte".</p>
            </div>
        </div>
        <div class="hidden p-4 rounded-lg bg-white shadow-md" id="caja" role="tabpanel" aria-labelledby="caja-tab">
            <h2 class="text-xl font-bold mb-4">Reporte de Caja</h2>
            <div id="caja-content">
                <p>Seleccione un rango de fechas y presione "Generar Reporte".</p>
            </div>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Elementos del DOM ---
    const generateReportBtn = document.getElementById('generate-report-btn');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const tabsElement = document.getElementById('report-tabs');

    // --- Inicialización de Pestañas (Tabs) ---
    // Asegúrate de que el script de Flowbite esté cargado si usas su API de JS
    // Si no, esta parte puede necesitar un inicializador de pestañas simple.
    // Por ahora, asumimos que la clase 'hidden' y los clics en botones manejan las pestañas.
    const tabs = document.querySelectorAll('[data-tabs-target]');
    const tabContents = document.querySelectorAll('[role="tabpanel"]');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Ocultar todos los contenidos
            tabContents.forEach(content => content.classList.add('hidden'));
            
            // Desactivar todas las pestañas
            tabs.forEach(t => {
                t.classList.remove('text-blue-600', 'border-blue-600');
                t.classList.add('text-gray-500', 'border-transparent');
            });

            // Mostrar el contenido de la pestaña seleccionada
            const target = document.querySelector(tab.dataset.tabsTarget);
            if (target) {
                target.classList.remove('hidden');
            }

            // Activar la pestaña seleccionada
            tab.classList.add('text-blue-600', 'border-blue-600');
            tab.classList.remove('text-gray-500', 'border-transparent');
        });
    });


    // --- Evento del Botón "Generar Reporte" ---
    generateReportBtn.addEventListener('click', () => {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (!startDate || !endDate) {
            alert('Por favor, seleccione un rango de fechas.');
            return;
        }

        const activeTab = document.querySelector('.tab-content:not(.hidden)');
        if (!activeTab) return;

        switch (activeTab.id) {
            case 'facturacion-content':
                fetchFacturacionReport(startDate, endDate);
                break;
            case 'citas-content':
                fetchCitasReport(startDate, endDate);
                break;
            case 'caja-content':
                fetchCajaReport(startDate, endDate);
                break;
        }
    });

    // --- Funciones Auxiliares ---
    const formatCurrency = (num) => new Intl.NumberFormat('es-CR', { style: 'currency', currency: 'CRC' }).format(num);

    function getStatusColor(status) {
        switch (status) {
            case 'Completada': return 'bg-green-100 text-green-800';
            case 'Pendiente': return 'bg-yellow-100 text-yellow-800';
            case 'Confirmada': return 'bg-purple-100 text-purple-800';
            case 'Cancelada': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    function getMovementTypeColor(type) {
        switch (type) {
            case 'INGRESO': return 'bg-green-100 text-green-800';
            case 'EGRESO': return 'bg-red-100 text-red-800';
            case 'APERTURA': return 'bg-blue-100 text-blue-800';
            case 'CIERRE': return 'bg-gray-100 text-gray-800';
            default: return 'bg-yellow-100 text-yellow-800';
        }
    }

    // --- Funciones para Obtener Reportes ---

    async function fetchFacturacionReport(startDate, endDate) {
        const contentDiv = document.getElementById('facturacion-content');
        contentDiv.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando reporte...</div>';
        try {
            const response = await fetch(`reporte_facturacion.php?start=${startDate}&end=${endDate}`);
            const result = await response.json();
            if (!result.success) throw new Error(result.message);
            const { summary, details } = result.data;
            let html = `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">...</div>`; // (Contenido del HTML del reporte)
            contentDiv.innerHTML = '...'; // (Lógica para renderizar el HTML completo)
        } catch (error) {
            contentDiv.innerHTML = `<p class="text-center text-red-500 p-4">Error al cargar el reporte: ${error.message}</p>`;
        }
    }

    async function fetchCitasReport(startDate, endDate) {
        const contentDiv = document.getElementById('citas-content');
        contentDiv.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando reporte...</div>';
        try {
            const response = await fetch(`reporte_citas.php?start=${startDate}&end=${endDate}`);
            const result = await response.json();
            if (!result.success) throw new Error(result.message);
            const { summary, details } = result.data;
            let html = '...'; // (Lógica para renderizar el HTML completo)
            contentDiv.innerHTML = html;
        } catch (error) {
            contentDiv.innerHTML = `<p class="text-center text-red-500 p-4">Error al cargar el reporte: ${error.message}</p>`;
        }
    }

    async function fetchCajaReport(startDate, endDate) {
        const contentDiv = document.getElementById('caja-content');
        contentDiv.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando reporte...</div>';
        try {
            const response = await fetch(`reporte_caja.php?start=${startDate}&end=${endDate}`);
            const result = await response.json();
            if (!result.success) throw new Error(result.message);
            const { summary, details } = result.data;
            let html = '...'; // (Lógica para renderizar el HTML completo)
            contentDiv.innerHTML = html;
        } catch (error) {
            contentDiv.innerHTML = `<p class="text-center text-red-500 p-4">Error al cargar el reporte: ${error.message}</p>`;
        }
    }
});
</script>
