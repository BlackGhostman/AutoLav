<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación - AutoSpa Blue Line</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal { transition: opacity 0.3s ease-in-out; }
        @media print {
            body * { visibility: hidden; }
            #receipt-modal-content, #receipt-modal-content * { visibility: visible; }
            #receipt-modal-content {
                position: absolute; left: 0; top: 0; width: 100%;
                margin: 0; padding: 10px; font-size: 10pt;
            }
            .no-print, .no-print * { display: none !important; visibility: hidden !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

<?php 
$currentPage = 'facturacion';
include 'menu.php'; 
?>

<main id="main-content" class="lg:ml-64 flex-grow p-4 md:p-8 no-print">
    <header class="mb-8 text-center">
        <h1 class="text-4xl font-bold text-gray-800">Servicios por Facturar</h1>
        <p class="text-gray-500">Seleccione los servicios para generar una factura.</p>
    </header>

    <div class="bg-white rounded-lg shadow p-6">
        <div id="lista-facturar" class="space-y-4">
        </div>
        <div class="mt-6 border-t pt-4 flex justify-end">
            <button id="facturar-btn" class="py-2 px-6 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow disabled:bg-blue-300 disabled:cursor-not-allowed" disabled>Facturar Seleccionados</button>
        </div>
    </div>
</main>

<div id="billing-modal" class="modal fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 hidden opacity-0 no-print">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-xl font-semibold mb-4">Resumen de Factura</h3>
        <div id="billing-summary-list" class="text-sm space-y-2 mb-4 max-h-40 overflow-y-auto"></div>
        <div class="space-y-4 border-t pt-4">
            <div><label for="discount-input" class="block text-sm font-medium text-gray-600">Descuento</label><input type="text" id="discount-input" placeholder="0" class="w-full mt-1 p-2 border rounded text-right"></div>
            <div><label for="payment-method" class="block text-sm font-medium text-gray-600">Forma de Pago</label><select id="payment-method" class="w-full mt-1 p-2 border rounded"><option>Efectivo</option><option>Datafono Simplif.</option><option>Datafono Lavacar</option><option>Banco Nacional</option><option>Sinpe Móvil</option></select></div>
            <div id="cash-payment-section" class="hidden"><label for="cash-received" class="block text-sm font-medium text-gray-600">Paga con</label><input type="text" id="cash-received" placeholder="0" class="w-full mt-1 p-2 border rounded text-right"></div>
        </div>
        <div class="border-t pt-4 mt-4 space-y-2 font-medium">
            <div class="flex justify-between"><span>Subtotal:</span><span id="billing-subtotal"></span></div>
            <div id="billing-discount-line" class="flex justify-between text-red-600"></div>
            <div class="flex justify-between text-2xl font-bold"><span>Total:</span><span id="billing-total"></span></div>
            <div id="billing-change-line" class="flex justify-between text-green-600"></div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button id="cancel-billing-button" class="py-2 px-4 bg-gray-300 rounded-lg">Cancelar</button>
            <button id="confirm-payment-button" class="py-2 px-4 bg-green-600 text-white rounded-lg">Pagar</button>
        </div>
    </div>
</div>

<div id="receipt-modal" class="modal fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 hidden opacity-0">
    <div id="receipt-modal-content" class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full text-sm">
        <h2 class="text-xl font-bold text-center mb-1">AUTOSPA BLUE LINE</h2>
        <p class="text-center text-xs text-gray-500 mb-4">¡Gracias por su preferencia!</p>
        <div id="receipt-header" class="mb-4"></div>
        <p class="font-semibold border-b pb-1 mb-2">Detalle del Servicio:</p>
        <div id="receipt-details" class="space-y-1"></div>
        <div id="receipt-summary" class="mt-4 pt-3 border-t border-dashed"></div>
        <p class="text-xs text-gray-500 text-center mt-4" id="receipt-date"></p>
        <div class="mt-5 flex gap-3 no-print">
            <button id="close-receipt-button" class="w-full py-2 bg-gray-600 text-white rounded-lg">Cerrar</button>
            <button id="print-receipt-button" class="w-full py-2 bg-blue-600 text-white rounded-lg">Imprimir</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const listaContainer = document.getElementById('lista-facturar');
    const facturarBtn = document.getElementById('facturar-btn');
    const billingModal = document.getElementById('billing-modal');
    const cancelBillingBtn = document.getElementById('cancel-billing-button');
    const confirmPaymentBtn = document.getElementById('confirm-payment-button');
    const summaryList = document.getElementById('billing-summary-list');
    const subtotalEl = document.getElementById('billing-subtotal');
    const discountInput = document.getElementById('discount-input');
    const discountLine = document.getElementById('billing-discount-line');
    const totalEl = document.getElementById('billing-total');
    const paymentMethodSelect = document.getElementById('payment-method');
    const cashSection = document.getElementById('cash-payment-section');
    const cashReceivedInput = document.getElementById('cash-received');
    const changeLine = document.getElementById('billing-change-line');
    const receiptModal = document.getElementById('receipt-modal');
    const closeReceiptBtn = document.getElementById('close-receipt-button');
    const printReceiptBtn = document.getElementById('print-receipt-button');
    let servicesData = [];

    const formatCurrency = (num) => new Intl.NumberFormat('es-CR', { style: 'currency', currency: 'CRC' }).format(num);
    const handleCurrencyInput = (e) => { let value = e.target.value.replace(/\D/g, ''); e.target.value = value ? new Intl.NumberFormat('de-DE').format(value) : ''; };

    async function fetchServicesToBill() {
        const response = await fetch('listar_servicios_pendientes.php');
        const result = await response.json();
        if (result.success) {
            servicesData = result.data;
            renderServices();
        }
    }

    function renderServices() {
        listaContainer.innerHTML = '';
        if (servicesData.length === 0) {
            listaContainer.innerHTML = '<p class="text-center text-gray-500">No hay servicios listos para facturar.</p>';
            return;
        }

        const servicesByClient = servicesData.reduce((acc, service) => {
            const clientId = service.id_cliente || 'general';
            if (!acc[clientId]) {
                acc[clientId] = {
                    nombre_cliente: service.nombre_cliente || 'Cliente General',
                    servicios: []
                };
            }
            acc[clientId].servicios.push(service);
            return acc;
        }, {});

        for (const clientId in servicesByClient) {
            const clientData = servicesByClient[clientId];
            const groupContainer = document.createElement('div');
            groupContainer.className = 'client-group border p-4 rounded-lg';
            groupContainer.dataset.clientId = clientId;

            const header = document.createElement('div');
            header.className = 'flex justify-between items-center mb-3 pb-2 border-b';
            header.innerHTML = `
                <h3 class="font-bold text-lg">${clientData.nombre_cliente}</h3>
                <button class="select-all-btn text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-1 px-2 rounded" data-cliente-id="${clientId}">Seleccionar Todos</button>
            `;
            groupContainer.appendChild(header);

            clientData.servicios.forEach(s => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between bg-white py-2';
                const statusBadge = s.nombre_estado === 'En Proceso' 
                    ? `<span class="text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-1 rounded-full">En Proceso</span>`
                    : `<span class="text-xs font-semibold bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Para Facturar</span>`;

                item.innerHTML = `
                    <div class="flex items-center">
                        <input type="checkbox" data-id="${s.id_servicio}" data-cliente-id="${clientId}" class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 service-checkbox">
                        <div class="ml-4">
                            <p class="font-semibold text-gray-800">${s.placa}</p>
                            <p class="text-xs text-gray-500">ID Servicio: ${s.id_servicio}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">${formatCurrency(s.total_servicio)}</p>
                        ${statusBadge}
                    </div>
                `;
                groupContainer.appendChild(item);
            });
            listaContainer.appendChild(groupContainer);
        }
    }

    function updateBillingModal() { const selectedIds = Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => parseInt(cb.dataset.id)); const servicesToBill = servicesData.filter(s => selectedIds.includes(parseInt(s.id_servicio))); summaryList.innerHTML = ''; let subtotal = 0; servicesToBill.forEach(s => { summaryList.innerHTML += `<div class="flex justify-between"><span>Placa ${s.placa}</span><span>${formatCurrency(s.total_servicio)}</span></div>`; subtotal += parseFloat(s.total_servicio); }); const discount = parseFloat(discountInput.value.replace(/\D/g, '')) || 0; const total = subtotal - discount; const cashReceived = parseFloat(cashReceivedInput.value.replace(/\D/g, '')) || 0; subtotalEl.textContent = formatCurrency(subtotal); discountLine.innerHTML = discount > 0 ? `<span>Descuento:</span><span>-${formatCurrency(discount)}</span>` : ''; totalEl.textContent = formatCurrency(total); if (paymentMethodSelect.value === 'Efectivo') { cashSection.classList.remove('hidden'); if (cashReceived >= total) { changeLine.innerHTML = `<span>Vuelto:</span><span>${formatCurrency(cashReceived - total)}</span>`; } else { changeLine.innerHTML = ''; } } else { cashSection.classList.add('hidden'); changeLine.innerHTML = ''; } }
    async function processPayment() { const selectedIds = Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => parseInt(cb.dataset.id)); const subtotal = servicesData.filter(s => selectedIds.includes(parseInt(s.id_servicio))).reduce((acc, s) => acc + parseFloat(s.total_servicio), 0); const discount = parseFloat(discountInput.value.replace(/\D/g, '')) || 0; const total = subtotal - discount; const formData = new URLSearchParams(); selectedIds.forEach(id => { formData.append('id_servicio[]', id); }); formData.append('subtotal', subtotal); formData.append('descuento', discount); formData.append('total', total); formData.append('metodo_pago', paymentMethodSelect.value); const response = await fetch('procesar_pago.php', { method: 'POST', body: formData }); const result = await response.json(); if (result.success) { closeBillingModal(); showPrintableReceipt(result.id_factura); fetchServicesToBill(); } else { alert(result.message); } }
    // Función para formatear moneda, si no existe globalmente
if (typeof formatCurrency === 'undefined') {
    window.formatCurrency = (value) => {
        return new Intl.NumberFormat('es-CR', { style: 'currency', currency: 'CRC' }).format(value);
    };
}

// Función para crear e imprimir el tiquete
function printTicket(factura, vehiculos, detalles) {
    const cliente = vehiculos[0] || {};
    let ticketContent = `
        <style>
            body {
                font-family: 'Courier New', Courier, monospace;
                width: 280px; /* Ancho para impresora de tiquetes */
                font-size: 10px;
                line-height: 1.4;
                margin: 0;
                padding: 10px;
            }
            .center { text-align: center; }
            .item { display: flex; justify-content: space-between; }
            .total { font-weight: bold; border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px;}
            h3, p { margin: 2px 0; }
            hr { border: none; border-top: 1px dashed #000; margin: 5px 0; }
        </style>
        <div class="center">
            <h3>Auto Lavado</h3>
            <p>Gracias por su visita</p>
            <p>${new Date(factura.fecha_factura).toLocaleString('es-CR')}</p>
        </div>
        <hr>
        <p>Factura #: ${factura.id_factura}</p>
        <p>Cliente: ${cliente.nombre_cliente || 'N/A'}</p>
        <p>Placa(s): ${vehiculos.map(v => v.placa).join(', ')}</p>
        <hr>
    `;

    detalles.forEach(d => {
        ticketContent += `
            <div class="item">
                <span>${d.nombre_servicio}</span>
                <span>${formatCurrency(d.precio_cobrado)}</span>
            </div>
        `;
    });

    ticketContent += '<hr>';

    ticketContent += `
        <div class="item">
            <span>Subtotal:</span>
            <span>${formatCurrency(factura.subtotal)}</span>
        </div>
    `;

    if (factura.descuento > 0) {
        ticketContent += `
            <div class="item">
                <span>Descuento:</span>
                <span>-${formatCurrency(factura.descuento)}</span>
            </div>
        `;
    }

    ticketContent += `
        <div class="item total">
            <span>TOTAL:</span>
            <span>${formatCurrency(factura.total_pagado)}</span>
        </div>
        <br>
        <div class="center">
            <p>¡Vuelva pronto!</p>
        </div>
    `;

    const printWindow = window.open('', 'PRINT', 'height=600,width=400');
    printWindow.document.write('<html><head><title>Factura</title></head><body>');
    printWindow.document.write(ticketContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}

async function showPrintableReceipt(facturaId) {
    const response = await fetch(`obtener_detalle_factura.php?id_factura=${facturaId}`);
    const result = await response.json();
    if (!result.success) {
        alert('Error al obtener los detalles de la factura.');
        return;
    }
    const { factura, vehiculos, detalles } = result.data;
    const cliente = vehiculos[0] || {};
    document.getElementById('receipt-header').innerHTML = `<div class="grid grid-cols-2 gap-x-4 gap-y-1"><div><strong>Factura #:</strong> ${factura.id_factura}</div><div><strong>Placa(s):</strong> ${vehiculos.map(v => v.placa).join(', ')}</div><div class="col-span-2"><strong>Cliente:</strong> ${cliente.nombre_cliente || 'N/A'}</div><div><strong>Cédula:</strong> ${cliente.cedula || 'N/A'}</div></div>`;
    const detailsContainer = document.getElementById('receipt-details');
    detailsContainer.innerHTML = '';
    detalles.forEach(d => {
        detailsContainer.innerHTML += `<div class="flex justify-between"><span>${d.nombre_servicio}</span><span>${formatCurrency(d.precio_cobrado)}</span></div>`;
    });
    let summaryHTML = `<div class="flex justify-between"><span>Subtotal:</span><span>${formatCurrency(factura.subtotal)}</span></div>`;
    if (factura.descuento > 0) {
        summaryHTML += `<div class="flex justify-between text-red-600"><span>Descuento:</span><span>-${formatCurrency(factura.descuento)}</span></div>`;
    }
    summaryHTML += `<div class="flex justify-between font-bold text-base mt-1 border-t pt-1"><span>Total Pagado:</span><span>${formatCurrency(factura.total_pagado)}</span></div>`;
    document.getElementById('receipt-summary').innerHTML = summaryHTML;
    document.getElementById('receipt-date').textContent = new Date(factura.fecha_factura).toLocaleString('es-CR');
    
    openReceiptModal();
    
    printTicket(factura, vehiculos, detalles);
}
    function openBillingModal() { updateBillingModal(); billingModal.classList.remove('hidden'); setTimeout(() => billingModal.classList.remove('opacity-0'), 10); }
    function closeBillingModal() { billingModal.classList.add('opacity-0'); setTimeout(() => billingModal.classList.add('hidden'), 300); }
    function openReceiptModal() { receiptModal.classList.remove('hidden'); setTimeout(() => receiptModal.classList.remove('opacity-0'), 10); }
    function closeReceiptModal() { receiptModal.classList.add('opacity-0'); setTimeout(() => receiptModal.classList.add('hidden'), 300); }

    listaContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('select-all-btn')) {
            const clientId = e.target.dataset.clienteId;
            const clientCheckboxes = document.querySelectorAll(`.service-checkbox[data-cliente-id="${clientId}"]`);
            const shouldSelectAll = Array.from(clientCheckboxes).some(cb => !cb.checked);
            clientCheckboxes.forEach(cb => cb.checked = shouldSelectAll);
            const anyChecked = document.querySelector('.service-checkbox:checked');
            facturarBtn.disabled = !anyChecked;
        }
    });
    
    listaContainer.addEventListener('change', (e) => {
        if (e.target.classList.contains('service-checkbox')) {
            const anyChecked = document.querySelector('.service-checkbox:checked');
            facturarBtn.disabled = !anyChecked;
        }
    });

    [discountInput, cashReceivedInput].forEach(input => input.addEventListener('input', (e) => { handleCurrencyInput(e); updateBillingModal(); }));
    paymentMethodSelect.addEventListener('change', updateBillingModal);
    facturarBtn.addEventListener('click', openBillingModal);
    cancelBillingBtn.addEventListener('click', closeBillingModal);
    confirmPaymentBtn.addEventListener('click', processPayment);
    closeReceiptBtn.addEventListener('click', closeReceiptModal);
    printReceiptBtn.addEventListener('click', () => window.print());
    fetchServicesToBill();
});
</script>
</body>
</html>
