<?php 
$currentPage = 'citas';
include 'header.php'; 
?>

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

    // --- STATE ---
    let placaTimeout;
    let cedulaTimeout;
    let extraServices = [];

    // --- DATA FETCHING & POPULATION ---
    async function fetchInitialData() {
        try {
            const response = await fetch('get_servicios.php');
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            
            extraServices = data.extraServices || [];
            const vehicleTypes = data.vehicleTypes || [];

            populateVehicleTypes(vehicleTypes);
            populateExtraServices();
        } catch (error) {
            console.error('Error fetching initial data:', error);
            alert('No se pudieron cargar los datos iniciales. Verifique la conexión.');
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

    async function fetchWashTypes(vehicleType) {
        dom.washTypeOptions.innerHTML = '<div class="flex justify-center items-center p-4"><div class="loader"></div></div>';
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

    function populateWashTypes(services) {
        dom.washTypeOptions.innerHTML = "";
        if (services.length === 0) {
            dom.washTypeOptions.innerHTML = '<p class="text-gray-500 text-center">No hay lavados base definidos.</p>';
        } else {
            services.forEach(service => {
                const id = `wash-${service.id}`;
                const div = document.createElement('div');
                div.className = 'flex items-center';
                div.innerHTML = `<input id="${id}" name="wash-type" type="radio" value="${service.name}" class="h-4 w-4 text-blue-600"><label for="${id}" class="ml-3 block text-sm text-gray-800">${service.name} (₡${service.price.toLocaleString('es-CR')})</label>`;
                dom.washTypeOptions.appendChild(div);
            });
        }
    }

    function populateExtraServices() {
        dom.extraServicesOptions.innerHTML = "";
        extraServices.forEach(service => {
            const id = `extra-${service.id}`;
            const div = document.createElement('div');
            div.className = 'flex items-center';
            div.innerHTML = `<input id="${id}" name="extra-service" type="checkbox" value="${service.name}" class="h-4 w-4 text-blue-600 rounded"><label for="${id}" class="ml-3 block text-sm text-gray-800">${service.name} (₡${service.price.toLocaleString('es-CR')})</label>`;
            dom.extraServicesOptions.appendChild(div);
        });
    }

    // --- SEARCH LOGIC ---
    async function buscarVehiculoPorPlaca(placa) {
        dom.placaLoader.classList.remove('hidden');
        try {
            const response = await fetch(`get_vehicle_data.php?placa=${placa}`);
            const result = await response.json();

            if (result.success && result.data) {
                const data = result.data;
                dom.cedula.value = data.cedula_cliente || '';
                dom.nombre.value = data.nombre_cliente || '';
                dom.telefono.value = data.celular_cliente || '';
                dom.email.value = data.email_cliente || '';
                
                if (data.tipo_vehiculo && dom.vehicleType.querySelector(`option[value="${data.tipo_vehiculo}"]`)) {
                    dom.vehicleType.value = data.tipo_vehiculo;
                    dom.vehicleType.dispatchEvent(new Event('change'));
                }
            } else {
                dom.nombre.value = '';
                dom.telefono.value = '';
                dom.email.value = '';
                dom.cedula.value = '';
            }
        } catch (error) {
            console.error('Error al buscar datos del vehículo.', error);
        } finally {
            dom.citaDetailsSection.classList.remove('hidden');
            dom.placaLoader.classList.add('hidden');
        }
    }

    async function buscarClientePorCedula(cedula) {
        dom.cedulaLoader.classList.remove('hidden');
        try {
            let response = await fetch(`get_vehicle_data.php?cedula=${cedula}`);
            let result = await response.json();

            if (result.success && result.data) {
                const data = result.data;
                dom.nombre.value = data.nombre_cliente || '';
                dom.telefono.value = data.celular_cliente || '';
                dom.email.value = data.email_cliente || '';
            } else if (cedula.length === 9) {
                response = await fetch(`buscar_cedula.php?cedula=${cedula}`);
                result = await response.json();

                if (result.success && result.nombreCompleto) {
                    dom.nombre.value = result.nombreCompleto;
                    dom.telefono.value = '';
                    dom.email.value = '';
                }
            }
        } catch (error) {
            console.error('Error al buscar datos del cliente por cédula.', error);
        } finally {
            dom.cedulaLoader.classList.add('hidden');
        }
    }

    // --- EVENT LISTENERS ---
    dom.placa.addEventListener('input', (e) => {
        e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
        clearTimeout(placaTimeout);
        if (e.target.value.length >= 3) {
            placaTimeout = setTimeout(() => {
                buscarVehiculoPorPlaca(e.target.value);
            }, 300);
        } else {
            dom.citaDetailsSection.classList.add('hidden');
        }
    });

    dom.cedula.addEventListener('input', (e) => {
        clearTimeout(cedulaTimeout);
        if (dom.nombre.value === '' && e.target.value.length >= 9) {
            cedulaTimeout = setTimeout(() => {
                buscarClientePorCedula(e.target.value);
            }, 300);
        }
    });

    dom.vehicleType.addEventListener('change', async (e) => {
        const selectedVehicle = e.target.value;
        if (selectedVehicle) {
            dom.washTypeContainer.classList.remove('hidden');
            dom.extraServicesContainer.classList.remove('hidden');
            await fetchWashTypes(selectedVehicle);
        } else {
            dom.washTypeContainer.classList.add('hidden');
            dom.extraServicesContainer.classList.add('hidden');
        }
    });

    // --- FORM SUBMISSION LOGIC ---
    dom.citaForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // --- VALIDACIÓN DEL LADO DEL CLIENTE ---
        const requiredFields = {
            'Placa': dom.placa.value,
            'Nombre del Cliente': dom.nombre.value,
            'Fecha de la Cita': dom.citaFecha.value,
            'Hora de la Cita': dom.citaHora.value
        };

        const missingFields = Object.entries(requiredFields)
            .filter(([_, value]) => !value.trim())
            .map(([key, _]) => key);

        if (missingFields.length > 0) {
            alert(`Por favor, complete los siguientes campos obligatorios:\n- ${missingFields.join('\n- ')}`);
            return;
        }
        
        const selectedWashType = dom.washTypeOptions.querySelector('input[name="wash-type"]:checked');
        const selectedExtraServices = Array.from(dom.extraServicesOptions.querySelectorAll('input[name="extra-service"]:checked'))
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

    // --- INITIALIZATION ---
    fetchInitialData();
});
</script>
<?php include 'footer.php'; ?>