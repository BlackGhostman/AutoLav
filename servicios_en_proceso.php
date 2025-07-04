<?php require_once 'proteger_pagina.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios en Proceso - AutoSpa Blue Line</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal { transition: opacity 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

<?php 
$currentPage = 'servicios_en_proceso';
include 'menu.php'; 
?>

<main id="main-content" class="lg:ml-64 flex-grow p-4 md:p-8">
    <header class="mb-8 text-center">
        <h1 class="text-4xl font-bold text-gray-800">Servicios en Proceso</h1>
        <p class="text-gray-500">Lista de vehículos que se están atendiendo actualmente. <span id="last-updated" class="text-xs font-mono"></span></p>
    </header>

    <div id="lista-servicios" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <p id="loading-message" class="text-center col-span-full">Cargando servicios...</p>
    </div>
</main>

<div id="finalize-modal" class="modal fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 hidden opacity-0">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full text-center">
        <h3 class="text-lg font-semibold mb-2">Finalizar Servicio</h3>
        <p class="text-sm text-gray-600 mb-4">
            ¿Desea parar el tiempo para el vehículo con placa <strong id="modal-placa"></strong> y pasarlo a facturación?
        </p>
        <div id="modal-details" class="text-xs text-left bg-gray-50 p-3 rounded-md mb-6">
        </div>
        <div class="flex justify-end gap-3">
            <button id="cancel-finalize-button" class="py-2 px-4 bg-gray-300 rounded-lg hover:bg-gray-400 transition">Cancelar</button>
            <button id="confirm-finalize-button" class="py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Sí, Finalizar</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const listaServiciosContainer = document.getElementById('lista-servicios');
    const loadingMessage = document.getElementById('loading-message');
    const lastUpdatedEl = document.getElementById('last-updated');
    const modal = document.getElementById('finalize-modal');
    const modalPlaca = document.getElementById('modal-placa');
    const modalDetails = document.getElementById('modal-details');
    const cancelButton = document.getElementById('cancel-finalize-button');
    const confirmButton = document.getElementById('confirm-finalize-button');
    let currentServiceId = null;

    async function fetchServicios() {
        try {
            const response = await fetch('listar_servicios.php');
            const result = await response.json();

            listaServiciosContainer.innerHTML = '';

            if (result.success && result.data.length > 0) {
                result.data.forEach(servicio => {
                    const card = document.createElement('div');
                    card.className = 'bg-white p-5 rounded-lg shadow hover:shadow-md transition-shadow';
                    
                    const horaInicio = new Date(servicio.hora_inicio).toLocaleTimeString('es-CR', { hour: '2-digit', minute: '2-digit' });

                    card.innerHTML = `
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="text-2xl font-bold text-gray-800">${servicio.placa}</h2>
                            <span class="text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-1 rounded-full">En Proceso</span>
                        </div>
                        <p class="text-sm text-gray-600">${servicio.tipo_vehiculo}</p>
                        <p class="text-sm text-gray-500">${servicio.nombre_cliente || 'Cliente no especificado'}</p>
                        <p class="text-xs text-gray-400 mt-3">Inició: ${horaInicio}</p>
                        <button data-id="${servicio.id_servicio}" class="finalize-btn w-full mt-4 py-2 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Finalizar</button>
                    `;
                    listaServiciosContainer.appendChild(card);
                });
            } else {
                listaServiciosContainer.innerHTML = '<p class="text-center col-span-full text-gray-500">No hay servicios en proceso en este momento.</p>';
            }
            lastUpdatedEl.textContent = `(Actualizado: ${new Date().toLocaleTimeString('es-CR')})`;
        } catch (error) {
            console.error('Error fetching services:', error);
            listaServiciosContainer.innerHTML = '<p class="text-center col-span-full text-red-500">Error al cargar los servicios.</p>';
        }
    }

    function openModal(servicioId) {
        const serviceCard = document.querySelector(`.finalize-btn[data-id='${servicioId}']`).closest('div.bg-white');
        const placa = serviceCard.querySelector('h2').textContent;
        const detalles = serviceCard.querySelectorAll('p');
        
        currentServiceId = servicioId;
        modalPlaca.textContent = placa;
        modalDetails.innerHTML = `
            <p><strong>Tipo:</strong> ${detalles[0].textContent}</p>
            <p><strong>Cliente:</strong> ${detalles[1].textContent}</p>
            <p><strong>${detalles[2].textContent}</strong></p>
        `;
        
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.remove('opacity-0'), 10);
    }

    function closeModal() {
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    async function finalizeService() {
        if (!currentServiceId) return;
        try {
            const response = await fetch('finalizar_servicio.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_servicio: currentServiceId })
            });
            const result = await response.json();

            if(result.success) {
                fetchServicios(); 
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error finalizing service:', error);
            alert('Error de conexión al intentar finalizar el servicio.');
        } finally {
            closeModal();
        }
    }

    listaServiciosContainer.addEventListener('click', (e) => {
        const finalizeBtn = e.target.closest('.finalize-btn');
        if (finalizeBtn) {
            const serviceId = finalizeBtn.getAttribute('data-id');
            openModal(serviceId);
        }
    });

    cancelButton.addEventListener('click', closeModal);
    confirmButton.addEventListener('click', finalizeService);
    fetchServicios();
    setInterval(fetchServicios, 30000);
});
</script>
</body>
</html>
