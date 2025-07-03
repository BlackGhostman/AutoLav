<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas - AutoSpa Blue Line</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .loader {
            border: 2px solid #e5e7eb; /* gray-200 */
            border-top: 2px solid #3b82f6; /* blue-500 */
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

<?php 
$currentPage = 'citas';
include 'menu.php'; 
?>

<main id="main-content" class="lg:ml-64 flex-grow p-4 md:p-8">
    <div class="container mx-auto">
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-gray-800">Gestión de Citas</h1>
            <p class="text-gray-500">Programe, modifique o cancele citas de servicios.</p>
        </header>

        <!-- Formulario para Crear Cita -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-8 max-w-4xl mx-auto">
            <h2 class="text-2xl font-semibold mb-4 border-b pb-3">Programar Nueva Cita</h2>
            <form id="cita-form" class="space-y-6 mt-4">
                <!-- Paso 1: Placa -->
                <div>
                    <label for="placa" class="block text-sm font-medium text-gray-700">Placa del Vehículo</label>
                    <div class="relative">
                        <input type="text" id="placa" maxlength="7" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 uppercase" placeholder="ABC123">
                        <div id="placa-loader" class="absolute right-3 top-1/2 -translate-y-1/2 hidden loader"></div>
                    </div>
                </div>

                <!-- Contenido que se muestra después de ingresar la placa -->
                <div id="cita-details-section" class="hidden space-y-6">
                <!-- Información del Cliente -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div>
                        <label for="cedula" class="block text-sm font-medium text-gray-700">Cédula</label>
                        <div class="relative">
                            <input type="number" id="cedula" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <div id="cedula-loader" class="absolute right-3 top-1/2 -translate-y-1/2 hidden loader"></div>
                        </div>
                    </div>
                    <div>
                        <label for="cliente-nombre" class="block text-sm font-medium text-gray-700">Nombre del Cliente</label>
                        <input type="text" id="cliente-nombre" name="cliente-nombre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label for="cliente-telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="tel" id="cliente-telefono" name="cliente-telefono" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo</label>
                        <input type="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Fecha y Hora de la Cita -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cita-fecha" class="block text-sm font-medium text-gray-700">Fecha de la Cita</label>
                        <input type="date" id="cita-fecha" name="cita-fecha" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label for="cita-hora" class="block text-sm font-medium text-gray-700">Hora de la Cita</label>
                        <input type="time" id="cita-hora" name="cita-hora" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>

                <!-- Vehículo y Servicios -->
                <div class="space-y-4 pt-4 border-t">
                    <label class="block text-lg font-semibold text-gray-800">Vehículo y Servicios</label>
                    <div>
                        <label for="vehicle-type" class="block text-sm font-medium text-gray-700">Tipo de Vehículo</label>
                        <select id="vehicle-type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></select>
                    </div>
                    <div id="wash-type-container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700">Tipo de Lavado</label>
                        <div id="wash-type-options" class="space-y-2 border border-gray-200 p-4 rounded-lg mt-1">
                            <p class="text-gray-500">Seleccione un tipo de vehículo primero...</p>
                        </div>
                    </div>
                    <div id="extra-services-container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Servicios adicionales</label>
                        <div id="extra-services-options" class="space-y-2 max-h-48 overflow-y-auto pr-2 border border-gray-200 rounded-lg p-4"></div>
                    </div>
                </div>

                <!-- Notas y Botón -->
                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700">Notas Adicionales</label>
                    <textarea id="notas" name="notas" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" class="py-2 px-6 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow">Guardar Cita</button>
                    </div>
                </div> <!-- Cierre de cita-details-section -->
            </form>
        </div>
    </div>
</main>

<script src="js/servicios.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- DOM REFERENCES ---
    const dom = {
        citaForm: document.getElementById('cita-form'),
        placa: document.getElementById('placa'),
        placaLoader: document.getElementById('placa-loader'),
        citaDetailsSection: document.getElementById('cita-details-section'),
        cedula: document.getElementById('cedula'),
        cedulaLoader: document.getElementById('cedula-loader'),
        nombre: document.getElementById('cliente-nombre'),
        telefono: document.getElementById('cliente-telefono'),
        email: document.getElementById('email'),
        vehicleType: document.getElementById('vehicle-type'),
        washTypeContainer: document.getElementById('wash-type-container'),
        washTypeOptions: document.getElementById('wash-type-options'),
        extraServicesContainer: document.getElementById('extra-services-container'),
        extraServicesOptions: document.getElementById('extra-services-options'),
        citaFecha: document.getElementById('cita-fecha'),
        citaHora: document.getElementById('cita-hora'),
        notas: document.getElementById('notas'),
    };

    // --- SERVICE POPULATION LOGIC ---
    function populateVehicleTypes() {
        dom.vehicleType.innerHTML = '<option value="" disabled selected>-- Elija una opción --</option>';
        Object.keys(serviciosPorTipo).forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            dom.vehicleType.appendChild(option);
        });
    }

    function populateWashTypes(vehicle) {
        const services = serviciosPorTipo[vehicle]?.baseServices || [];
        dom.washTypeOptions.innerHTML = '';
        if (services.length > 0) {
            services.forEach(service => {
                const label = document.createElement('label');
                label.className = 'flex items-center space-x-3 cursor-pointer';
                const input = document.createElement('input');
                input.type = 'radio';
                input.name = 'wash-type';
                input.value = service.name;
                input.dataset.price = service.price;
                input.className = 'form-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500';
                label.appendChild(input);
                const span = document.createElement('span');
                span.textContent = `${service.name} (₡${service.price.toLocaleString('es-CR')})`;
                label.appendChild(span);
                dom.washTypeOptions.appendChild(label);
            });
        } else {
            dom.washTypeOptions.innerHTML = '<p class="text-gray-500">No hay servicios de lavado para este tipo de vehículo.</p>';
        }
    }

    function populateExtraServices() {
        dom.extraServicesOptions.innerHTML = '';
        serviciosAdicionales.forEach(service => {
            const label = document.createElement('label');
            label.className = 'flex items-center space-x-3 cursor-pointer';
            const input = document.createElement('input');
            input.type = 'checkbox';
            input.name = 'extra-services';
            input.value = service.name; // Use name for saving
            input.dataset.price = service.price;
            input.className = 'form-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500';
            label.appendChild(input);
            const span = document.createElement('span');
            span.textContent = `${service.name} (₡${service.price.toLocaleString('es-CR')})`;
            label.appendChild(span);
            dom.extraServicesOptions.appendChild(label);
        });
    }

    // --- PLATE SEARCH LOGIC ---
    let placaTimeout;
    dom.placa.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
        clearTimeout(placaTimeout);
        if (e.target.value.length >= 3) {
            dom.placaLoader.classList.remove('hidden');
            placaTimeout = setTimeout(() => {
                buscarVehiculoPorPlaca(e.target.value);
            }, 500);
        } else {
            dom.placaLoader.classList.add('hidden');
        }
    });

    async function buscarVehiculoPorPlaca(placa) {
        try {
            const response = await fetch(`buscar_vehiculo.php?placa=${placa}`);
            const result = await response.json();

            if (result && result.success && result.data) {
                const vehiculo = result.data;
                if (vehiculo.tipo_vehiculo) {
                    dom.vehicleType.value = vehiculo.tipo_vehiculo;
                    dom.vehicleType.dispatchEvent(new Event('change'));
                }
                if (vehiculo.cliente) {
                    dom.cedula.value = vehiculo.cliente.cedula || '';
                    dom.nombre.value = vehiculo.cliente.nombre || '';
                    dom.telefono.value = vehiculo.cliente.celular || '';
                    dom.email.value = vehiculo.cliente.email || '';
                }
            }
        } catch (error) {
            console.error('Error al buscar vehículo:', error);
        } finally {
            dom.citaDetailsSection.classList.remove('hidden');
            dom.placaLoader.classList.add('hidden');
        }
    }

    // --- CLIENT SEARCH LOGIC ---
    let cedulaTimeout;
    dom.cedula.addEventListener('input', () => {
        clearTimeout(cedulaTimeout);
        if (dom.cedula.value.length >= 9) {
            dom.cedulaLoader.classList.remove('hidden');
            cedulaTimeout = setTimeout(() => {
                buscarClientePorCedula(dom.cedula.value);
            }, 500);
        } else {
            dom.cedulaLoader.classList.add('hidden');
        }
    });

    async function buscarClientePorCedula(cedula) {
        try {
            const response = await fetch(`buscar_cliente.php?cedula=${cedula}`);
            const result = await response.json();
            if (result && result.success && result.data) {
                dom.nombre.value = result.data.nombre || '';
                dom.telefono.value = result.data.celular || '';
                dom.email.value = result.data.email || '';
            }
        } catch (error) {
            console.error('Error al buscar cliente:', error);
        } finally {
            dom.cedulaLoader.classList.add('hidden');
        }
    }

    // --- FORM SUBMISSION LOGIC ---
    dom.citaForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const selectedWashType = dom.washTypeOptions.querySelector('input[name="wash-type"]:checked');
        const selectedExtraServices = Array.from(dom.extraServicesOptions.querySelectorAll('input[name="extra-services"]:checked'))
                                           .map(input => input.value);

        const citaData = {
            placa: dom.placa.value,
            cliente_cedula: dom.cedula.value,
            cliente_nombre: dom.nombre.value,
            cliente_telefono: dom.telefono.value,
            cliente_email: dom.email.value,
            fecha_cita: dom.citaFecha.value,
            hora_cita: dom.citaHora.value,
            tipo_vehiculo: dom.vehicleType.value,
            tipo_lavado: selectedWashType ? selectedWashType.value : null,
            servicios_adicionales: selectedExtraServices,
            notas: dom.notas.value
        };

        try {
            const response = await fetch('guardar_cita.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(citaData)
            });
            const result = await response.json();
            if (result.success) {
                alert('Cita guardada exitosamente.');
                dom.citaForm.reset();
                dom.citaDetailsSection.classList.add('hidden');
                dom.washTypeContainer.classList.add('hidden');
                dom.extraServicesContainer.classList.add('hidden');
            } else {
                alert('Error al guardar la cita: ' + (result.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error de red:', error);
            alert('Error de red al intentar guardar la cita.');
        }
    });

    dom.vehicleType.addEventListener('change', (e) => {
        const selectedVehicle = e.target.value;
        if (selectedVehicle) {
            populateWashTypes(selectedVehicle);
            dom.washTypeContainer.classList.remove('hidden');
            dom.extraServicesContainer.classList.remove('hidden');
        } else {
            dom.washTypeContainer.classList.add('hidden');
            dom.extraServicesContainer.classList.add('hidden');
        }
    });

    // --- INITIALIZATION ---
    populateVehicleTypes();
    populateExtraServices();
});
</script>
</body>
</html>