<?php 
require_once 'proteger_pagina.php'; 
$currentPage = 'facturas_electronicas';
include 'header.php'; 
?>

<body class="bg-gray-100">
<main id="main-content" class="lg:ml-64 flex-grow p-4 md:p-8">
    <header class="mb-8 text-center">
        <h1 class="text-4xl font-bold text-gray-800">Gestión de Facturas Electrónicas</h1>
        <p class="text-gray-500">Aquí se muestran las facturas electrónicas pendientes de envío.</p>
    </header>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-700">Facturas Pendientes</h3>
        <div id="lista-facturas-electronicas" class="overflow-x-auto">
            <!-- Los datos se cargarán aquí dinámicamente -->
        </div>
    </div>
</main>

<!-- Modal para Detalles de Factura -->
<div id="detalle-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-2xl w-full">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-xl font-semibold">Detalles de la Factura</h3>
            <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800">&times;</button>
        </div>
        <div id="modal-body" class="mb-6">
            <!-- Contenido del modal se carga aquí -->
        </div>
        <div class="flex justify-end gap-4">
            <button id="modal-cancel-btn" class="py-2 px-4 bg-gray-300 rounded-lg">Cerrar</button>
            <button id="modal-confirm-sent-btn" class="py-2 px-4 bg-indigo-600 text-white rounded-lg">Marcar como Enviada</button>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const listaContainer = document.getElementById('lista-facturas-electronicas');
    const modal = document.getElementById('detalle-modal');
    const modalBody = document.getElementById('modal-body');
    const confirmSentBtn = document.getElementById('modal-confirm-sent-btn');
    let facturasData = [];

    const formatCurrency = (num) => new Intl.NumberFormat('es-CR', { style: 'currency', currency: 'CRC' }).format(num);

    async function fetchPendingElectronicInvoices() {
        listaContainer.innerHTML = '<p class="text-center text-gray-500">Cargando facturas pendientes...</p>';
        try {
            const response = await fetch('listar_facturas_electronicas_pendientes.php');
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                facturasData = result.data;
                renderInvoices(facturasData);
            } else {
                listaContainer.innerHTML = '<p class="text-center text-gray-500">No hay facturas electrónicas pendientes.</p>';
            }
        } catch (error) {
            console.error('Error fetching electronic invoices:', error);
            listaContainer.innerHTML = '<p class="text-center text-red-500">Error al cargar las facturas.</p>';
        }
    }

    function renderInvoices(invoices) {
        listaContainer.innerHTML = '';
        const table = document.createElement('table');
        table.className = 'min-w-full divide-y divide-gray-200';
        table.innerHTML = `
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Factura</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente Cédula</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200"></tbody>
        `;
        const tbody = table.querySelector('tbody');
        invoices.forEach(factura => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${factura.id_factura}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(factura.fecha_factura).toLocaleString('es-CR')}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${factura.cliente_cedula}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">${formatCurrency(factura.total_pagado)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button class="ver-detalles-btn text-indigo-600 hover:text-indigo-900" data-id="${factura.id_factura}">Ver Detalles</button>
                </td>
            `;
            tbody.appendChild(row);
        });
        listaContainer.appendChild(table);
    }

    function openModal(facturaId) {
        const factura = facturasData.find(f => f.id_factura == facturaId);
        if (!factura) return;

        let detailsHtml = `
            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                <div><strong>Factura #:</strong> ${factura.id_factura}</div>
                <div><strong>Fecha:</strong> ${new Date(factura.fecha_factura).toLocaleString('es-CR')}</div>
                <div><strong>Cédula:</strong> ${factura.cliente_cedula}</div>
                <div><strong>Email:</strong> ${factura.cliente_email}</div>
            </div>
            <h4 class="font-semibold text-md mb-2 border-t pt-4">Servicios Incluidos</h4>
            <ul class="list-disc pl-5 space-y-1">
        `;

        factura.detalles.forEach(d => {
            detailsHtml += `<li>Placa ${d.placa}: ${d.nombre_servicio} - ${formatCurrency(d.precio_cobrado)}</li>`;
        });

        detailsHtml += `</ul><div class="text-right font-bold text-lg mt-4 border-t pt-2">Total: ${formatCurrency(factura.total_pagado)}</div>`;

        modalBody.innerHTML = detailsHtml;
        confirmSentBtn.dataset.id = facturaId; // Guardar el ID en el botón
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    async function markAsSent(facturaId) {
        if (!confirm('¿Está seguro de que desea marcar esta factura como enviada?')) return;

        try {
            const formData = new URLSearchParams({ id_factura: facturaId });
            const response = await fetch('marcar_factura_enviada.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (result.success) {
                alert('Factura marcada como enviada.');
                closeModal();
                fetchPendingElectronicInvoices();
            } else {
                alert(`Error: ${result.message}`);
            }
        } catch (error) {
            console.error('Error marking as sent:', error);
            alert('Ocurrió un error de red.');
        }
    }

    listaContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('ver-detalles-btn')) {
            openModal(e.target.dataset.id);
        }
    });

    document.getElementById('close-modal-btn').addEventListener('click', closeModal);
    document.getElementById('modal-cancel-btn').addEventListener('click', closeModal);
    confirmSentBtn.addEventListener('click', (e) => markAsSent(e.target.dataset.id));


    // Carga inicial
    fetchPendingElectronicInvoices();
});
</script>

<?php include 'footer.php'; ?>
