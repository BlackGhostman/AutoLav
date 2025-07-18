<?php 
require_once 'proteger_pagina.php'; // Guardián de seguridad
$currentPage = 'reportes';
include 'header.php'; 
?>

<main id="main-content" class="lg:ml-64 p-6">

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Reportes</h1>

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
            <div class="self-end flex gap-2">
                <button id="filter-btn" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">Generar Reporte</button>
                <button id="export-pdf-btn" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors" style="display: none;"><i class="fas fa-file-pdf mr-2"></i>Exportar PDF</button>
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
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="servicios-tab" data-tabs-target="#servicios" type="button" role="tab" aria-controls="servicios" aria-selected="false">Servicios</button>
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
        <div class="hidden p-4 rounded-lg bg-white shadow-md" id="servicios" role="tabpanel" aria-labelledby="servicios-tab">
            <h2 class="text-xl font-bold mb-4">Reporte de Servicios</h2>
            <div id="servicios-content">
                <p>Seleccione un rango de fechas y presione "Generar Reporte".</p>
            </div>
        </div>
    </div>

</main>

<!-- Librerías para exportar a PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Elementos del DOM ---
    const generateReportBtn = document.getElementById('filter-btn');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const exportPdfBtn = document.getElementById('export-pdf-btn');
    const tabs = document.querySelectorAll('[data-tabs-target]');
    const tabContents = document.querySelectorAll('[role="tabpanel"]');

    // --- Lógica para Exportar a PDF ---
    const exportToPdf = () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        const activeTab = document.querySelector('[role="tabpanel"]:not(.hidden)');
        const table = activeTab.querySelector('table');
        const title = activeTab.querySelector('h2').textContent;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (!table) {
            alert('No hay datos para exportar.');
            return;
        }

        doc.text(title, 14, 16);
        doc.setFontSize(10);
        doc.text(`Período: ${startDate} al ${endDate}`, 14, 22);

        doc.autoTable({
            html: table,
            startY: 28,
            theme: 'grid',
            headStyles: { fillColor: [22, 160, 133] }, // Color verde azulado
        });

        doc.save(`${title.replace(/ /g, '_')}_${new Date().toISOString().slice(0, 10)}.pdf`);
    };

    exportPdfBtn.addEventListener('click', exportToPdf);

    // --- Lógica de Pestañas (Tabs) ---

    // Estado inicial: Activar la primera pestaña por defecto
    document.getElementById('facturacion-tab').classList.add('text-blue-600', 'border-blue-600', 'border-b-2');
    document.getElementById('facturacion-tab').classList.remove('hover:text-gray-600', 'hover:border-gray-300');
    document.getElementById('facturacion').classList.remove('hidden'); // ID CORREGIDO

    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            tabContents.forEach(content => content.classList.add('hidden'));
            tabs.forEach(t => {
                t.classList.remove('text-blue-600', 'border-blue-600', 'border-b-2');
                t.classList.add('text-gray-500', 'border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
            });
            const target = document.querySelector(tab.dataset.tabsTarget);
            if (target) target.classList.remove('hidden');
            tab.classList.add('text-blue-600', 'border-blue-600', 'border-b-2');
            tab.classList.remove('text-gray-500', 'border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');

        // Ocultar botón de PDF al cambiar de pestaña
        exportPdfBtn.style.display = 'none';
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

        const activeTab = document.querySelector('[role="tabpanel"]:not(.hidden)');
        if (!activeTab) return;

        // Mostrar el botón de exportar después de generar un reporte
        exportPdfBtn.style.display = 'inline-block';

        // IDs CORREGIDOS en el switch
        switch (activeTab.id) {
            case 'facturacion':
                fetchFacturacionReport(startDate, endDate);
                break;
            case 'citas':
                fetchCitasReport(startDate, endDate);
                break;
            case 'caja':
                fetchCajaReport(startDate, endDate);
                break;
            case 'servicios':
                fetchServiciosReport(startDate, endDate);
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



    // --- Funciones para Obtener Reportes ---
    async function fetchFacturacionReport(startDate, endDate) {
        const contentDiv = document.getElementById('facturacion-content');
        contentDiv.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando reporte...</div>';
        try {
            const response = await fetch(`reporte_facturacion.php?start=${startDate}&end=${endDate}`);
            const result = await response.json();
            if (!result.success) throw new Error(result.message);
            const { summary, details } = result.data;
            let html = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Total Facturado</p><p class="text-2xl font-bold">${formatCurrency(summary.total_facturado || 0)}</p></div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">N° de Facturas</p><p class="text-2xl font-bold">${summary.numero_facturas || 0}</p></div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Promedio por Factura</p><p class="text-2xl font-bold">${formatCurrency(summary.promedio_factura || 0)}</p></div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Total Descuentos</p><p class="text-2xl font-bold text-red-600">-${formatCurrency(summary.total_descuentos || 0)}</p></div>
                </div>
                <div class="overflow-x-auto bg-white rounded-lg shadow"><table class="min-w-full"><thead class="bg-gray-50"><tr><th class="text-left py-3 px-4 font-semibold text-sm">N° Factura</th><th class="text-left py-3 px-4 font-semibold text-sm">Fecha</th><th class="text-left py-3 px-4 font-semibold text-sm">Cliente</th><th class="text-right py-3 px-4 font-semibold text-sm">Monto</th></tr></thead><tbody class="text-gray-700">`;
            if (details.length > 0) {
                details.forEach(factura => {
                    html += `<tr class="border-b border-gray-200 hover:bg-gray-50"><td class="py-3 px-4">${factura.id_factura}</td><td class="py-3 px-4">${new Date(factura.fecha_factura).toLocaleString('es-CR')}</td><td class="py-3 px-4">${factura.nombre_cliente || 'N/A'}</td><td class="py-3 px-4 text-right font-medium">${formatCurrency(factura.total_pagado)}</td></tr>`;
                });
            } else {
                html += '<tr><td colspan="4" class="text-center py-4">No se encontraron facturas en este período.</td></tr>';
            }
            html += `</tbody></table></div>`;
            contentDiv.innerHTML = html;
        } catch (error) {
            contentDiv.innerHTML = `<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert"><strong class="font-bold">Error:</strong><span class="block sm:inline"> ${error.message}</span></div>`;
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
            let html = `
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Total de Citas</p><p class="text-2xl font-bold text-blue-800">${summary.Total || 0}</p></div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Completadas</p><p class="text-2xl font-bold text-green-800">${summary.Completada || 0}</p></div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Pendientes</p><p class="text-2xl font-bold text-yellow-800">${summary.Pendiente || 0}</p></div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Confirmadas</p><p class="text-2xl font-bold text-purple-800">${summary.Confirmada || 0}</p></div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Canceladas</p><p class="text-2xl font-bold text-red-800">${summary.Cancelada || 0}</p></div>
                </div>
                <div class="overflow-x-auto bg-white rounded-lg shadow"><table class="min-w-full"><thead class="bg-gray-50"><tr><th class="text-left py-3 px-4 font-semibold text-sm">ID Cita</th><th class="text-left py-3 px-4 font-semibold text-sm">Fecha</th><th class="text-left py-3 px-4 font-semibold text-sm">Cliente</th><th class="text-left py-3 px-4 font-semibold text-sm">Placa</th><th class="text-left py-3 px-4 font-semibold text-sm">Estado</th></tr></thead><tbody class="text-gray-700">`;
            if (details.length > 0) {
                details.forEach(cita => {
                    html += `<tr class="border-b border-gray-200 hover:bg-gray-50"><td class="py-3 px-4">${cita.id_cita}</td><td class="py-3 px-4">${new Date(cita.fecha_cita).toLocaleString('es-CR')}</td><td class="py-3 px-4">${cita.nombre_cliente || 'N/A'}</td><td class="py-3 px-4">${cita.placa || 'N/A'}</td><td class="py-3 px-4"><span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full ${getStatusColor(cita.estado)}">${cita.estado}</span></td></tr>`;
                });
            } else {
                html += '<tr><td colspan="5" class="text-center py-4">No se encontraron citas en este período.</td></tr>';
            }
            html += `</tbody></table></div>`;
            contentDiv.innerHTML = html;
        } catch (error) {
            contentDiv.innerHTML = `<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert"><strong class="font-bold">Error:</strong><span class="block sm:inline"> ${error.message}</span></div>`;
        }
    }

    async function fetchServiciosReport(startDate, endDate) {
        const contentDiv = document.getElementById('servicios-content');
        contentDiv.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando reporte...</div>';
        try {
            const response = await fetch(`reporte_servicios.php?start=${startDate}&end=${endDate}`);
            const result = await response.json();
            if (!result.success) throw new Error(result.message);
            const details = result.data;
            let html = `<div class="overflow-x-auto bg-white rounded-lg shadow"><table class="min-w-full"><thead class="bg-gray-50"><tr>
                            <th class="text-left py-3 px-4 font-semibold text-sm">Placa</th>
                            <th class="text-left py-3 px-4 font-semibold text-sm">Tipo Vehículo</th>
                            <th class="text-left py-3 px-4 font-semibold text-sm">Servicio</th>
                            <th class="text-right py-3 px-4 font-semibold text-sm">Duración (min)</th>
                            <th class="text-left py-3 px-4 font-semibold text-sm">Inicio</th>
                            <th class="text-left py-3 px-4 font-semibold text-sm">Fin</th>
                        </tr></thead><tbody class="text-gray-700">`;
            if (details.length > 0) {
                details.forEach(item => {
                    html += `<tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4">${item.placa || 'N/A'}</td>
                                <td class="py-3 px-4">${item.tipo_vehiculo || 'N/A'}</td>
                                <td class="py-3 px-4">${item.nombre_servicio || 'N/A'}</td>
                                <td class="py-3 px-4 text-right">${item.duracion_minutos === null ? 'En proceso' : item.duracion_minutos}</td>
                                <td class="py-3 px-4">${item.hora_inicio ? new Date(item.hora_inicio).toLocaleString('es-CR') : 'N/A'}</td>
                                <td class="py-3 px-4">${item.hora_final ? new Date(item.hora_final).toLocaleString('es-CR') : 'N/A'}</td>
                            </tr>`;
                });
            } else {
                html += '<tr><td colspan="6" class="text-center py-4">No se encontraron servicios en este período.</td></tr>';
            }
            html += `</tbody></table></div>`;
            contentDiv.innerHTML = html;
        } catch (error) {
            contentDiv.innerHTML = `<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert"><strong class="font-bold">Error:</strong><span class="block sm:inline"> ${error.message}</span></div>`;
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

            const getDiferenciaColor = (diferencia) => {
                const val = parseFloat(diferencia);
                if (val > 0) return 'text-green-600';
                if (val < 0) return 'text-red-600';
                return 'text-gray-700';
            };

            let html = `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Total Ventas</p><p class="text-2xl font-bold">${formatCurrency(summary.total_ventas || 0)}</p></div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Monto Final Real</p><p class="text-2xl font-bold text-blue-800">${formatCurrency(summary.total_monto_final || 0)}</p></div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Total Diferencias</p><p class="text-2xl font-bold ${getDiferenciaColor(summary.total_diferencia)}">${formatCurrency(summary.total_diferencia || 0)}</p></div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow text-center"><p class="text-sm text-gray-500">Sesiones Cerradas</p><p class="text-2xl font-bold">${summary.numero_sesiones || 0}</p></div>
                </div>
                <div class="overflow-x-auto bg-white rounded-lg shadow"><table class="min-w-full"><thead class="bg-gray-50"><tr>
                    <th class="text-left py-3 px-4 font-semibold text-sm">ID Sesión</th>
                    <th class="text-left py-3 px-4 font-semibold text-sm">Fecha Cierre</th>
                    <th class="text-right py-3 px-4 font-semibold text-sm">M. Inicial</th>
                    <th class="text-right py-3 px-4 font-semibold text-sm">T. Ventas</th>
                    <th class="text-right py-3 px-4 font-semibold text-sm">M. Calculado</th>
                    <th class="text-right py-3 px-4 font-semibold text-sm">M. Real</th>
                    <th class="text-right py-3 px-4 font-semibold text-sm">Diferencia</th>
                </tr></thead><tbody class="text-gray-700">`;
            
            if (details.length > 0) {
                details.forEach(sesion => {
                    html += `<tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-4">${sesion.id}</td>
                        <td class="py-3 px-4">${new Date(sesion.fecha_cierre).toLocaleString('es-CR')}</td>
                        <td class="py-3 px-4 text-right">${formatCurrency(sesion.monto_inicial)}</td>
                        <td class="py-3 px-4 text-right">${formatCurrency(sesion.total_ventas)}</td>
                        <td class="py-3 px-4 text-right">${formatCurrency(sesion.monto_final_calculado)}</td>
                        <td class="py-3 px-4 text-right font-bold">${formatCurrency(sesion.monto_final_real)}</td>
                        <td class="py-3 px-4 text-right font-bold ${getDiferenciaColor(sesion.diferencia)}">${formatCurrency(sesion.diferencia)}</td>
                    </tr>`;
                });
            } else {
                html += '<tr><td colspan="7" class="text-center py-4">No se encontraron sesiones de caja cerradas en este período.</td></tr>';
            }
            html += `</tbody></table></div>`;
            contentDiv.innerHTML = html;
        } catch (error) {
            contentDiv.innerHTML = `<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert"><strong class="font-bold">Error:</strong><span class="block sm:inline"> ${error.message}</span></div>`;
        }
    }
});
</script>
<?php include 'footer.php'; ?>
