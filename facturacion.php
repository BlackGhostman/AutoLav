<?php require_once 'proteger_pagina.php'; ?>
<?php 
$currentPage = 'facturacion';
include 'header.php'; 
?>

<main id="main-content" class="lg:ml-64 flex-grow p-4 md:p-8 no-print">
    <header class="mb-8 text-center">
        <h1 class="text-4xl font-bold text-gray-800">Facturación</h1>
        <p class="text-gray-500">Generar nuevas facturas o reimprimir existentes.</p>
        <div class="mt-4 flex justify-center gap-4">
            <button id="show-facturar-btn" class="py-2 px-4 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition-all">Facturar Servicios</button>
            <button id="show-reimprimir-btn" class="py-2 px-4 bg-gray-500 text-white rounded-lg shadow-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-75 transition-all">Reimprimir Factura</button>
        </div>
    </header>

    <div id="facturar-section">
        <div class="bg-white rounded-lg shadow p-6">
            <div id="lista-facturar" class="space-y-4">
            </div>
            <div class="mt-6 border-t pt-4 flex justify-end">
                <button id="facturar-btn" class="py-2 px-6 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow disabled:bg-blue-300 disabled:cursor-not-allowed" disabled>Facturar Seleccionados</button>
            </div>
        </div>
    </div>

    <div id="reimprimir-section" class="hidden">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-700">Buscar Facturas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="search_factura_id" class="block text-sm font-medium text-gray-700">N° de Factura</label>
                    <input type="text" id="search_factura_id" class="mt-1 w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: 1024">
                </div>
                <div>
                    <label for="search_vehiculo_placa" class="block text-sm font-medium text-gray-700">Placa del Vehículo</label>
                    <input type="text" id="search_vehiculo_placa" class="mt-1 w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: ABC-123">
                </div>
                <div>
                    <label for="search_cliente_nombre" class="block text-sm font-medium text-gray-700">Nombre del Cliente</label>
                    <input type="text" id="search_cliente_nombre" class="mt-1 w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: John Doe">
                </div>
                <div>
                    <label for="search_cliente_cedula" class="block text-sm font-medium text-gray-700">Cédula del Cliente</label>
                    <input type="text" id="search_cliente_cedula" class="mt-1 w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: 123456789">
                </div>
            </div>
            <div class="flex justify-end">
                <button id="search-factura-btn" class="py-2 px-6 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Buscar</button>
            </div>
            <div class="mt-8 pt-6 border-t">
                <h3 class="text-lg font-semibold mb-4">Resultados</h3>
                <div id="lista-reimprimir" class="space-y-3">
                    <p class="text-center text-gray-500">Ingrese un término de búsqueda para ver las facturas.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<div id="billing-modal" class="modal fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 hidden opacity-0 no-print">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full">
        <h3 class="text-xl font-semibold mb-4">Resumen de Factura</h3>
        <div id="billing-summary-list" class="text-sm space-y-2 mb-4 max-h-40 overflow-y-auto"></div>
        <div class="space-y-4 border-t pt-4">
            <div><label for="discount-input" class="block text-sm font-medium text-gray-600">Descuento</label><input type="text" id="discount-input" placeholder="0" class="w-full mt-1 p-2 border rounded text-right"></div>
            <div><label for="payment-method" class="block text-sm font-medium text-gray-600">Forma de Pago</label><select id="payment-method-select" class="w-full mt-1 p-2 border rounded"><option>Cargando...</option></select></div>
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
    // --- Seletores de Elementos DOM ---
    const listaContainer = document.getElementById('lista-facturar');
    const facturarBtn = document.getElementById('facturar-btn');
    const billingModal = document.getElementById('billing-modal');
    const receiptModal = document.getElementById('receipt-modal');
    const paymentMethodSelect = document.getElementById('payment-method-select');
    const cashSection = document.getElementById('cash-payment-section');
    const cashReceivedInput = document.getElementById('cash-received');
    const discountInput = document.getElementById('discount-input');

    // --- Estado de la Aplicación ---
    let servicesData = [];

    // --- Funciones de Utilidad ---
    const formatCurrency = (num) => new Intl.NumberFormat('es-CR', { style: 'currency', currency: 'CRC' }).format(num);
    const handleCurrencyInput = (e) => { let value = e.target.value.replace(/\D/g, ''); e.target.value = value ? new Intl.NumberFormat('de-DE').format(value) : ''; };

    // --- Lógica de Negocio ---

    async function loadPaymentMethods() {
        try {
            const response = await fetch('get_formas_pago.php');
            const result = await response.json();
            if (result.success) {
                paymentMethodSelect.innerHTML = '';
                result.data.forEach(method => {
                    const option = document.createElement('option');
                    option.value = method.id_forma_pago;
                    option.textContent = method.nombre;
                    if (method.nombre.toLowerCase() === 'efectivo') {
                        option.selected = true;
                    }
                    paymentMethodSelect.appendChild(option);
                });
            } else {
                paymentMethodSelect.innerHTML = '<option>Error al cargar</option>';
            }
        } catch (error) {
            console.error('Error loading payment methods:', error);
            paymentMethodSelect.innerHTML = '<option>Error de conexión</option>';
        }
    }

    async function fetchServicesToBill() {
        try {
            const response = await fetch('listar_servicios_pendientes.php');
            const result = await response.json();
            if (result.success) {
                servicesData = result.data;
                renderServices();
            } else {
                 listaContainer.innerHTML = '<p class="text-center text-gray-500">No hay servicios listos para facturar.</p>';
            }
        } catch (error) {
            console.error('Error fetching services:', error);
            listaContainer.innerHTML = '<p class="text-center text-red-500">Error al cargar los servicios.</p>';
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
                acc[clientId] = { nombre_cliente: service.nombre_cliente || 'Cliente General', servicios: [] };
            }
            acc[clientId].servicios.push(service);
            return acc;
        }, {});

        for (const clientId in servicesByClient) {
            const clientData = servicesByClient[clientId];
            const groupContainer = document.createElement('div');
            groupContainer.className = 'client-group border p-4 rounded-lg mb-4';
            groupContainer.innerHTML = `
                <div class="flex justify-between items-center mb-3 pb-2 border-b">
                    <h3 class="font-bold text-lg">${clientData.nombre_cliente}</h3>
                    <button class="select-all-btn text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-1 px-2 rounded" data-cliente-id="${clientId}">Seleccionar Todos</button>
                </div>
            `;
            clientData.servicios.forEach(s => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between bg-white py-2';
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
                        <span class="text-xs font-semibold bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Para Facturar</span>
                    </div>
                `;
                groupContainer.appendChild(item);
            });
            listaContainer.appendChild(groupContainer);
        }
    }

    function updateBillingModal() {
        const selectedIds = Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => parseInt(cb.dataset.id));
        const servicesToBill = servicesData.filter(s => selectedIds.includes(parseInt(s.id_servicio)));
        const summaryList = document.getElementById('billing-summary-list');
        summaryList.innerHTML = '';
        let subtotal = servicesToBill.reduce((acc, s) => acc + parseFloat(s.total_servicio), 0);

        servicesToBill.forEach(s => {
            summaryList.innerHTML += `<div class="flex justify-between"><span>Placa ${s.placa}</span><span>${formatCurrency(s.total_servicio)}</span></div>`;
        });

        const discount = parseFloat(discountInput.value.replace(/\D/g, '')) || 0;
        const total = subtotal - discount;
        const cashReceived = parseFloat(cashReceivedInput.value.replace(/\D/g, '')) || 0;

        document.getElementById('billing-subtotal').textContent = formatCurrency(subtotal);
        document.getElementById('billing-discount-line').innerHTML = discount > 0 ? `<span>Descuento:</span><span>-${formatCurrency(discount)}</span>` : '';
        document.getElementById('billing-total').textContent = formatCurrency(total);
        
        const selectedPaymentMethod = paymentMethodSelect.options[paymentMethodSelect.selectedIndex].text;
        if (selectedPaymentMethod.toLowerCase() === 'efectivo') {
            cashSection.classList.remove('hidden');
            const changeLine = document.getElementById('billing-change-line');
            if (cashReceived >= total) {
                changeLine.innerHTML = `<span>Vuelto:</span><span>${formatCurrency(cashReceived - total)}</span>`;
            } else {
                changeLine.innerHTML = '';
            }
        } else {
            cashSection.classList.add('hidden');
            document.getElementById('billing-change-line').innerHTML = '';
        }
    }

    async function processPayment() {
        const selectedIds = Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => parseInt(cb.dataset.id));
        const subtotal = servicesData.filter(s => selectedIds.includes(parseInt(s.id_servicio))).reduce((acc, s) => acc + parseFloat(s.total_servicio), 0);
        const discount = parseFloat(discountInput.value.replace(/\D/g, '')) || 0;
        const formData = new URLSearchParams();
        selectedIds.forEach(id => formData.append('id_servicio[]', id));
        formData.append('subtotal', subtotal);
        formData.append('descuento', discount);
        formData.append('total', subtotal - discount);
        formData.append('id_forma_pago', paymentMethodSelect.value);

        try {
            const response = await fetch('procesar_pago.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                closeBillingModal();
                showPrintableReceipt(result.id_factura);
                fetchServicesToBill();
            } else {
                alert(`Error al procesar el pago: ${result.message}`);
            }
        } catch (error) {
            console.error('Error processing payment:', error);
            alert('Ocurrió un error de red al procesar el pago.');
        }
    }

    async function buscarFacturas() {
        const listaReimprimir = document.getElementById('lista-reimprimir');
        listaReimprimir.innerHTML = '<p class="text-center text-gray-500">Buscando...</p>';
        try {
            const params = new URLSearchParams({
                id_factura: document.getElementById('search_factura_id').value,
                placa: document.getElementById('search_vehiculo_placa').value,
                nombre: document.getElementById('search_cliente_nombre').value,
                cedula: document.getElementById('search_cliente_cedula').value
            });
            const response = await fetch(`buscar_facturas.php?${params}`);
            if (!response.ok) throw new Error(`Error del servidor: ${response.statusText}`);
            const result = await response.json();
            listaReimprimir.innerHTML = '';
            if (result.success && result.data.length > 0) {
                result.data.forEach(factura => {
                    const item = document.createElement('div');
                    item.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg';
                    const fecha = new Date(factura.fecha_factura).toLocaleString('es-CR', { dateStyle: 'short', timeStyle: 'short' });
                    item.innerHTML = `
                        <div class="flex-grow">
                            <div class="flex justify-between items-center">
                                <p class="font-semibold">Factura #${factura.id_factura}</p>
                                <p class="text-xs text-gray-500">${fecha}</p>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Cliente: ${factura.nombre_cliente || 'N/A'} - Placa: ${factura.placa || 'N/A'}</p>
                        </div>
                        <button class="reimprimir-btn text-sm bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded ml-4 flex-shrink-0" data-id="${factura.id_factura}">Reimprimir</button>
                    `;
                    listaReimprimir.appendChild(item);
                });
            } else {
                listaReimprimir.innerHTML = '<p class="text-center text-gray-500">No se encontraron facturas.</p>';
            }
        } catch (error) {
            console.error('Error al buscar facturas:', error);
            listaReimprimir.innerHTML = '<p class="text-center text-red-500">Ocurrió un error al buscar.</p>';
        }
    }

    async function showPrintableReceipt(facturaId) {
        try {
            const response = await fetch(`obtener_detalle_factura.php?id_factura=${facturaId}`);
            if (!response.ok) throw new Error('Network response was not ok.');
            const result = await response.json();
            if (!result.success) throw new Error(result.message);
            const { factura, vehiculos, detalles } = result.data;
            const cliente = vehiculos[0] || {};
            document.getElementById('receipt-header').innerHTML = `<div class="grid grid-cols-2 gap-x-4 gap-y-1"><div><strong>Factura #:</strong> ${factura.id_factura}</div><div><strong>Placa(s):</strong> ${vehiculos.map(v => v.placa).join(', ')}</div><div class="col-span-2"><strong>Cliente:</strong> ${cliente.nombre_cliente || 'N/A'}</div><div><strong>Cédula:</strong> ${cliente.cedula || 'N/A'}</div></div>`;
            const detailsContainer = document.getElementById('receipt-details');
            detailsContainer.innerHTML = '';
            detalles.forEach(d => { detailsContainer.innerHTML += `<div class="flex justify-between"><span>${d.nombre_servicio}</span><span>${formatCurrency(d.precio_cobrado)}</span></div>`; });
            let summaryHTML = `<div class="flex justify-between"><span>Subtotal:</span><span>${formatCurrency(factura.subtotal)}</span></div>`;
            if (factura.descuento > 0) summaryHTML += `<div class="flex justify-between text-red-600"><span>Descuento:</span><span>-${formatCurrency(factura.descuento)}</span></div>`;
            summaryHTML += `<div class="flex justify-between font-bold text-base mt-1 border-t pt-1"><span>Total Pagado:</span><span>${formatCurrency(factura.total_pagado)}</span></div>`;
            document.getElementById('receipt-summary').innerHTML = summaryHTML;
            document.getElementById('receipt-date').textContent = new Date(factura.fecha_factura).toLocaleString('es-CR');
            openReceiptModal();
        } catch (error) {
            console.error('Error showing receipt:', error);
            alert('No se pudo mostrar el recibo.');
        }
    }

    // --- Modales y Vistas ---
    function openBillingModal() { updateBillingModal(); billingModal.classList.remove('hidden'); setTimeout(() => billingModal.classList.remove('opacity-0'), 10); }
    function closeBillingModal() { billingModal.classList.add('opacity-0'); setTimeout(() => billingModal.classList.add('hidden'), 300); }
    function openReceiptModal() { receiptModal.classList.remove('hidden'); setTimeout(() => receiptModal.classList.remove('opacity-0'), 10); }
    function closeReceiptModal() { receiptModal.classList.add('opacity-0'); setTimeout(() => receiptModal.classList.add('hidden'), 300); }

    function setActiveView(view) {
        const facturarSection = document.getElementById('facturar-section');
        const reimprimirSection = document.getElementById('reimprimir-section');
        const showFacturarBtn = document.getElementById('show-facturar-btn');
        const showReimprimirBtn = document.getElementById('show-reimprimir-btn');
        if (view === 'facturar') {
            facturarSection.classList.remove('hidden');
            reimprimirSection.classList.add('hidden');
            showFacturarBtn.classList.replace('bg-gray-500', 'bg-blue-600');
            showReimprimirBtn.classList.replace('bg-blue-600', 'bg-gray-500');
        } else {
            facturarSection.classList.add('hidden');
            reimprimirSection.classList.remove('hidden');
            showFacturarBtn.classList.replace('bg-blue-600', 'bg-gray-500');
            showReimprimirBtn.classList.replace('bg-gray-500', 'bg-blue-600');
        }
    }

    // --- Event Listeners ---
    document.getElementById('show-facturar-btn').addEventListener('click', () => setActiveView('facturar'));
    document.getElementById('show-reimprimir-btn').addEventListener('click', () => setActiveView('reimprimir'));
    document.getElementById('search-factura-btn').addEventListener('click', buscarFacturas);
    facturarBtn.addEventListener('click', openBillingModal);
    document.getElementById('cancel-billing-button').addEventListener('click', closeBillingModal);
    document.getElementById('confirm-payment-button').addEventListener('click', processPayment);
    document.getElementById('close-receipt-button').addEventListener('click', closeReceiptModal);
    document.getElementById('print-receipt-button').addEventListener('click', () => window.print());
    [discountInput, cashReceivedInput].forEach(input => input.addEventListener('input', (e) => { handleCurrencyInput(e); updateBillingModal(); }));
    paymentMethodSelect.addEventListener('change', updateBillingModal);
    listaContainer.addEventListener('change', (e) => { if (e.target.classList.contains('service-checkbox')) { facturarBtn.disabled = !document.querySelector('.service-checkbox:checked'); } });
    listaContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('select-all-btn')) {
            const clientId = e.target.dataset.clienteId;
            const clientCheckboxes = document.querySelectorAll(`.service-checkbox[data-cliente-id="${clientId}"]`);
            const shouldSelectAll = Array.from(clientCheckboxes).some(cb => !cb.checked);
            clientCheckboxes.forEach(cb => cb.checked = shouldSelectAll);
            facturarBtn.disabled = !document.querySelector('.service-checkbox:checked');
        }
    });
    document.getElementById('lista-reimprimir').addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('reimprimir-btn')) {
            showPrintableReceipt(e.target.dataset.id);
        }
    });

    // --- Carga Inicial ---
    setActiveView('facturar');
    loadPaymentMethods();
    fetchServicesToBill();
});
</script>
<?php include 'footer.php'; ?>
