<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Servicio - AutoSpa Blue Line</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-input {
            width: 100%; padding: 0.75rem; border: 1px solid #d1d5db;
            border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            transition: ring 0.2s, border-color 0.2s;
        }
        .form-input:focus {
            outline: none; border-color: #3b82f6; --tw-ring-color: #3b82f6;
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
        }
        .loader {
            border: 2px solid #f3f3f3; border-top: 2px solid #3498db;
            border-radius: 50%; width: 16px; height: 16px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .form-section { transition: opacity 0.5s ease-in-out, max-height 0.5s ease-in-out; overflow: hidden; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

<?php 
$currentPage = 'registrar_servicio';
include 'menu.php'; 
?>

<main id="main-content" class="lg:ml-64 flex-grow">
    <div class="w-full max-w-2xl mx-auto p-6 md:p-8">
        <header class="mb-6 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Registrar Nuevo Servicio</h1>
            <p class="text-gray-500">Siga los pasos para iniciar un nuevo lavado.</p>
        </header>

        <div class="bg-white rounded-2xl shadow-lg p-8 space-y-8">
            <div>
                <label for="placa" class="block text-lg font-semibold text-gray-700 mb-2">Paso 1: Ingrese la Placa</label>
                <input type="text" id="placa" maxlength="7" class="form-input text-center text-xl tracking-widest" placeholder="ABC123">
            </div>

            <div id="client-section" class="form-section hidden opacity-0 max-h-0">
                <label class="block text-lg font-semibold text-gray-700 mb-3">Paso 2: Información del Cliente (Opcional)</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-gray-50 p-4 rounded-lg border">
                    <div>
                        <label for="cedula" class="block font-medium text-gray-600">Cédula</label>
                        <input type="number" id="cedula" class="form-input">
                    </div>
                    <div class="relative">
                        <label for="nombre" class="block font-medium text-gray-600">Nombre o Sociedad</label>
                        <input type="text" id="nombre" class="form-input">
                        <div id="nombre-loader" class="loader absolute right-2 top-8 hidden"></div>
                    </div>
                    <div>
                        <label for="celular" class="block font-medium text-gray-600">Celular</label>
                        <input type="tel" id="celular" maxlength="9" placeholder="1111-1111" class="form-input">
                    </div>
                    <div>
                        <label for="email" class="block font-medium text-gray-600">Correo</label>
                        <input type="email" id="email" class="form-input">
                    </div>
                </div>
            </div>

            <div id="vehicle-services-section" class="form-section hidden opacity-0 max-h-0 space-y-4">
                <label class="block text-lg font-semibold text-gray-700 mb-2">Paso 3: Seleccione Vehículo y Servicios</label>
                <div>
                     <label for="vehicle-type" class="block text-sm font-medium text-gray-700">Tipo de Vehículo</label>
                    <select id="vehicle-type" class="form-input mt-1"></select>
                </div>
                <div id="wash-type-container" class="form-section hidden opacity-0 max-h-0">
                     <label class="block text-sm font-medium text-gray-700">Tipo de Lavado</label>
                     <div id="wash-type-options" class="space-y-2 border p-4 rounded-lg mt-1 max-h-48 overflow-y-auto">
                        <p class="text-gray-500">Seleccione un tipo de vehículo primero...</p>
                    </div>
                </div>
                 <div id="extra-services-container" class="form-section hidden opacity-0 max-h-0">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Servicios adicionales</label>
                    <div id="extra-services-options" class="space-y-2 max-h-48 overflow-y-auto pr-2 border rounded-lg p-4"></div>
                </div>
            </div>

            <div id="finalize-section" class="form-section hidden opacity-0 max-h-0">
                 <div class="my-6 text-center">
                    <p class="text-lg font-semibold text-gray-700">Monto Total:</p>
                    <p id="total-amount" class="text-3xl font-bold text-blue-600">₡0</p>
                </div>
                 <div class="mb-4 text-center">
                    <label for="start-time" class="block text-lg font-semibold text-gray-700">Hora de Inicio</label>
                    <input type="time" id="start-time" class="form-input w-48 mx-auto mt-2 text-center">
                </div>
                <button id="start-wash-button" class="w-full py-4 px-4 bg-green-600 text-white text-xl font-bold rounded-lg hover:bg-green-700 transition shadow-lg">INICIAR LAVADO</button>
            </div>
            <div id="status-message" class="hidden"></div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- DOM ELEMENTS ---
    const dom = {
        placa: document.getElementById('placa'),
        clientSection: document.getElementById('client-section'),
        cedula: document.getElementById('cedula'),
        nombre: document.getElementById('nombre'),
        celular: document.getElementById('celular'),
        email: document.getElementById('email'),
        vehicleServicesSection: document.getElementById('vehicle-services-section'),
        vehicleType: document.getElementById('vehicle-type'),
        washTypeContainer: document.getElementById('wash-type-container'),
        washTypeOptions: document.getElementById('wash-type-options'),
        extraServicesContainer: document.getElementById('extra-services-container'),
        extraServicesOptions: document.getElementById('extra-services-options'),
        finalizeSection: document.getElementById('finalize-section'),
        startTime: document.getElementById('start-time'),
        totalAmount: document.getElementById('total-amount'),
        startWashButton: document.getElementById('start-wash-button'),
        statusMessage: document.getElementById('status-message'),
    };

    // --- STATE ---
    let extraServices = [];
    let currentOrder = { cita_id: null };
    let placaTimeout;
    let initialDataReady = false;

    // --- HELPER FUNCTIONS ---
    function showSection(section) {
        section.classList.remove('hidden', 'opacity-0', 'max-h-0');
        section.style.maxHeight = 'none';
        const realHeight = section.scrollHeight;
        section.style.maxHeight = realHeight + 'px';
        setTimeout(() => {
            section.style.maxHeight = section.scrollHeight + 'px';
        }, 50);
    }

    function hideSection(section) {
        section.style.maxHeight = '0px';
        section.classList.add('opacity-0');
        setTimeout(() => section.classList.add('hidden'), 500);
    }

    function updateParentSectionHeight(parentSection) {
        setTimeout(() => {
            if (!parentSection.classList.contains('hidden')) {
                parentSection.style.maxHeight = 'none'; 
                parentSection.style.maxHeight = parentSection.scrollHeight + 'px';
            }
        }, 300); 
    }

    function showStatusMessage(message, isSuccess) {
        dom.statusMessage.textContent = message;
        dom.statusMessage.className = `text-center p-3 my-4 rounded-lg ${isSuccess ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        dom.statusMessage.classList.remove('hidden');
        setTimeout(() => dom.statusMessage.classList.add('hidden'), 5000);
    }

    function setCurrentTime() {
        const now = new Date();
        dom.startTime.value = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    }
    
    function updateTotal() {
        let total = 0;
        const selectedWash = dom.washTypeOptions.querySelector('input[name="wash-type"]:checked');
        if (selectedWash) {
            total += parseFloat(selectedWash.dataset.price);
        }
        const selectedExtras = dom.extraServicesOptions.querySelectorAll('input[name="extra-service"]:checked');
        selectedExtras.forEach(extra => {
            total += parseFloat(extra.dataset.price);
        });
        dom.totalAmount.textContent = new Intl.NumberFormat("es-CR", { style: "currency", currency: "CRC", minimumFractionDigits: 0 }).format(total);
    }

    // --- DATA FETCHING AND POPULATION ---
    async function fetchInitialData() {
        dom.placa.disabled = true;
        dom.placa.placeholder = "Cargando...";
        try {
            const response = await fetch('get_servicios.php');
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            
            extraServices = data.extraServices || [];
            const vehicleTypes = data.vehicleTypes || [];

            populateVehicleTypes(vehicleTypes);
            populateExtraServices();

            initialDataReady = true;
            dom.placa.disabled = false;
            dom.placa.placeholder = "ABC123";
        } catch (error) {
            console.error('Error fetching initial data:', error);
            showStatusMessage('No se pudieron cargar los datos iniciales. Verifique la conexión.', false);
            dom.placa.placeholder = "Error al cargar";
        }
    }

    async function fetchWashTypes(vehicleType) {
        dom.washTypeOptions.innerHTML = '<div class="flex justify-center items-center p-4"><div class="loader"></div></div>';
        showSection(dom.washTypeContainer);
        try {
            const response = await fetch(`get_servicios.php?vehicle_type=${encodeURIComponent(vehicleType)}`);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            populateWashTypes(data.baseServices || []);
        } catch (error) {
            console.error(`Error fetching wash types for ${vehicleType}:`, error);
            dom.washTypeOptions.innerHTML = '<p class="text-red-500 text-center">Error al cargar los tipos de lavado.</p>';
        }
    }

    function populateVehicleTypes(vehicleTypes) {
        dom.vehicleType.innerHTML = '<option value="" disabled selected>-- Elija una opción --</option>';
        vehicleTypes.forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            dom.vehicleType.appendChild(option);
        });
    }

    function populateWashTypes(services) {
        dom.washTypeOptions.innerHTML = "";
        if (services.length === 0) {
            dom.washTypeOptions.innerHTML = '<p class="text-gray-500 text-center">No hay lavados base definidos para este tipo de vehículo.</p>';
        } else {
            services.forEach(service => {
                const id = `wash-${service.id}`;
                const div = document.createElement('div');
                div.className = 'flex items-center p-3 border rounded-lg hover:bg-gray-100 transition cursor-pointer';
                div.innerHTML = `<input id="${id}" name="wash-type" type="radio" value="${service.id}" data-price="${service.price}" data-name="${service.name}" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"><label for="${id}" class="ml-3 block text-sm font-medium text-gray-800 flex-grow cursor-pointer">${service.name}</label><span class="text-sm font-semibold">${new Intl.NumberFormat("es-CR", { style: "currency", currency: "CRC", minimumFractionDigits: 0 }).format(service.price)}</span>`;
                dom.washTypeOptions.appendChild(div);
            });
        }
        showSection(dom.washTypeContainer);
    }

    function populateExtraServices() {
        dom.extraServicesOptions.innerHTML = "";
        if (extraServices.length === 0) {
            dom.extraServicesOptions.innerHTML = '<p class="text-gray-500">No hay servicios adicionales disponibles.</p>';
            return;
        }
        extraServices.forEach(service => {
            const id = `extra-${service.id}`;
            const div = document.createElement('div');
            div.className = 'flex items-center p-3 border rounded-lg hover:bg-gray-100 transition cursor-pointer';
            div.innerHTML = `<input id="${id}" name="extra-service" type="checkbox" value="${service.id}" data-price="${service.price}" data-name="${service.name}" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"><label for="${id}" class="ml-3 block text-sm font-medium text-gray-800 flex-grow cursor-pointer">${service.name}</label><span class="text-sm font-semibold">${new Intl.NumberFormat("es-CR", { style: "currency", currency: "CRC", minimumFractionDigits: 0 }).format(service.price)}</span>`;
            dom.extraServicesOptions.appendChild(div);
        });
    }
    
    async function buscarVehiculoPorPlaca(placa) {
        if (placa.length < 3 || !initialDataReady) return;

        showSection(dom.clientSection);
        showSection(dom.vehicleServicesSection);

        try {
            const response = await fetch(`get_vehicle_data.php?placa=${placa}`);
            const result = await response.json();

            if (result.success && result.data) {
                const data = result.data;
                dom.cedula.value = data.cedula_cliente || '';
                dom.nombre.value = data.nombre_cliente || '';
                dom.celular.value = data.celular_cliente || '';
                dom.email.value = data.email_cliente || '';
                
                if (data.tipo_vehiculo && dom.vehicleType.querySelector(`option[value="${data.tipo_vehiculo}"]`)) {
                    dom.vehicleType.value = data.tipo_vehiculo;
                    dom.vehicleType.dispatchEvent(new Event('change'));
                }
            } else {
                // Si no se encuentra la placa, no limpiamos la cédula para permitir la búsqueda por este campo
                dom.nombre.value = '';
                dom.celular.value = '';
                dom.email.value = '';
                dom.cedula.value = ''; // Limpiamos la cédula si no se encuentra la placa
            }
        } catch (error) {
            console.error('Error al buscar datos del vehículo.', error);
        }
    }

    async function buscarClientePorCedula(cedula) {
        if (cedula.length < 9) return;

        try {
            // Primero, buscar en la base de datos local
            let response = await fetch(`get_vehicle_data.php?cedula=${cedula}`);
            let result = await response.json();

            if (result.success && result.data) {
                const data = result.data;
                dom.nombre.value = data.nombre_cliente || '';
                dom.celular.value = data.celular_cliente || '';
                dom.email.value = data.email_cliente || '';
            } else if (cedula.length === 9) {
                // Si no se encuentra y la cédula tiene 9 dígitos, buscar en el servicio externo
                response = await fetch(`buscar_cedula.php?cedula=${cedula}`);
                result = await response.json();

                if (result.success && result.nombreCompleto) {
                    dom.nombre.value = result.nombreCompleto;
                    // Dejar los otros campos vacíos para que el usuario los llene
                    dom.celular.value = '';
                    dom.email.value = '';
                }
            }
        } catch (error) {
            console.error('Error al buscar datos del cliente por cédula.', error);
        }
    }

    async function startWash() {
        const selectedWashInput = dom.washTypeOptions.querySelector('input[name="wash-type"]:checked');
        const selectedExtrasInputs = dom.extraServicesOptions.querySelectorAll('input[name="extra-service"]:checked');

        if (!dom.placa.value || !dom.vehicleType.value || !selectedWashInput) {
            showStatusMessage('Por favor complete la placa, tipo de vehículo y tipo de lavado.', false);
            return;
        }

        const baseWash = { name: selectedWashInput.dataset.name, price: parseFloat(selectedWashInput.dataset.price) };
        const extras = Array.from(selectedExtrasInputs).map(input => ({ name: input.dataset.name, price: parseFloat(input.dataset.price) }));

        const dataToSend = {
            placa: dom.placa.value, cedula: dom.cedula.value, nombre: dom.nombre.value,
            celular: dom.celular.value, email: dom.email.value, vehicleType: dom.vehicleType.value,
            startTime: dom.startTime.value, baseWash: baseWash, extras: extras, cita_id: currentOrder.cita_id
        };

        dom.startWashButton.textContent = 'INICIANDO...';
        dom.startWashButton.disabled = true;

        try {
            const response = await fetch('crear_orden.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(dataToSend)
            });
            const result = await response.json();
            if (response.ok && result.success) {
                showStatusMessage('¡Servicio iniciado con éxito!', true);
                setTimeout(() => { window.location.href = 'servicios_en_proceso.php'; }, 1500);
            } else {
                showStatusMessage(result.message || 'Error al crear la orden.', false);
                dom.startWashButton.textContent = 'INICIAR LAVADO';
                dom.startWashButton.disabled = false;
            }
        } catch (error) {
            console.error('Error en fetch:', error);
            showStatusMessage('Error de conexión. Por favor, inténtelo de nuevo.', false);
            dom.startWashButton.textContent = 'INICIAR LAVADO';
            dom.startWashButton.disabled = false;
        }
    }

    // --- EVENT LISTENERS ---
    dom.placa.addEventListener('input', (e) => {
        const sanitizedValue = e.target.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
        e.target.value = sanitizedValue;

        clearTimeout(placaTimeout);
        if (sanitizedValue.length >= 3) {
            placaTimeout = setTimeout(() => {
                buscarVehiculoPorPlaca(sanitizedValue);
            }, 300);
        } else {
            hideSection(dom.clientSection);
            hideSection(dom.vehicleServicesSection);
        }
    });

    dom.cedula.addEventListener('input', (e) => {
        const cedulaValue = e.target.value;
        // Solo buscar por cédula si la búsqueda por placa no arrojó resultados (nombre está vacío)
        if (dom.nombre.value === '') {
            buscarClientePorCedula(cedulaValue);
        }
    });

    async function handleVehicleTypeChange(selectedType) {
        if (selectedType) {
            await fetchWashTypes(selectedType);
            showSection(dom.extraServicesContainer);
        } else {
            hideSection(dom.washTypeContainer);
            hideSection(dom.extraServicesContainer);
        }
        hideSection(dom.finalizeSection);
        updateTotal();
        updateParentSectionHeight(dom.vehicleServicesSection);
    }

    dom.vehicleType.addEventListener('change', (e) => {
        handleVehicleTypeChange(e.target.value);
    });

    dom.vehicleServicesSection.addEventListener('change', () => {
        updateTotal();
        const selectedWash = dom.washTypeOptions.querySelector('input[name="wash-type"]:checked');
        if (selectedWash) {
            setCurrentTime();
            showSection(dom.finalizeSection);
        } else {
            hideSection(dom.finalizeSection);
        }
    });

    dom.startWashButton.addEventListener('click', startWash);

    // --- INITIALIZATION ---
    async function initializePage() {
        await fetchInitialData();

        const citaDataString = sessionStorage.getItem('citaParaServicio');
        if (!citaDataString) return;

        try {
            const citaData = JSON.parse(citaDataString);
            currentOrder.cita_id = citaData.id_cita;

            // Rellenar campos y deshabilitar la placa para evitar conflictos
            dom.placa.value = citaData.placa || '';
            dom.placa.disabled = true;
            dom.cedula.value = citaData.cedula_cliente || '';
            dom.nombre.value = citaData.nombre_cliente || '';
            dom.celular.value = citaData.telefono_cliente || '';
            dom.email.value = citaData.email_cliente || '';

            showSection(dom.clientSection);
            showSection(dom.vehicleServicesSection);

            // Buscar activamente los datos del vehículo (incluido el tipo) por placa
            let vehicleType = citaData.tipo_vehiculo; // Usar el de la cita como fallback
            if (citaData.placa) {
                try {
                    const response = await fetch(`get_vehicle_data.php?placa=${citaData.placa}`);
                    const result = await response.json();
                    if (result.success && result.data && result.data.tipo_vehiculo) {
                        vehicleType = result.data.tipo_vehiculo;
                    }
                } catch (error) {
                    console.error('Error al autocompletar datos del vehículo desde la cita.', error);
                }
            }

            // Con el tipo de vehículo, actualizar la UI y cargar los lavados
            if (vehicleType) {
                dom.vehicleType.value = vehicleType;
                await handleVehicleTypeChange(vehicleType);
            }

            // Ahora que los lavados están cargados, seleccionar el servicio base
            if (citaData.servicio_base) {
                const washRadio = dom.washTypeOptions.querySelector(`input[data-name="${citaData.servicio_base}"]`);
                if (washRadio) washRadio.checked = true;
            }

            // Seleccionar los servicios extras
            if (citaData.servicios_extras && Array.isArray(citaData.servicios_extras)) {
                citaData.servicios_extras.forEach(extraName => {
                    const extraCheckbox = dom.extraServicesOptions.querySelector(`input[data-name="${extraName}"]`);
                    if (extraCheckbox) extraCheckbox.checked = true;
                });
            }

            // Disparar el evento 'change' para actualizar el total final
            dom.vehicleServicesSection.dispatchEvent(new Event('change'));

        } catch (e) {
            console.error("Error al procesar datos de la cita:", e);
            dom.placa.disabled = false; // Re-habilitar en caso de error
        } finally {
            sessionStorage.removeItem('citaParaServicio');
        }
    }

    initializePage();
});
</script>
</body>
</html>
