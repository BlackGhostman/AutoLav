<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas del Día - AutoSpa Blue Line</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-900 text-white">

<?php 
$currentPage = 'citas_del_dia';
include 'menu.php'; 
?>

    <main id="main-content" class="lg:ml-64 transition-all duration-300 ease-in-out">
        <div class="container mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold text-white mb-6">Citas del Día</h1>
            
            <div id="citas-container" class="space-y-4">
                <!-- Las citas se cargarán aquí dinámicamente -->
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dom = {
                citasContainer: document.getElementById('citas-container')
            };

            async function fetchCitasDelDia() {
                try {
                    const response = await fetch('api_get_citas.php?fecha=hoy&estado=pendiente');
                    const citas = await response.json();
                    displayCitas(citas);
                } catch (error) {
                    console.error('Error fetching citas del día:', error);
                    dom.citasContainer.innerHTML = '<p class="text-red-400">Error al cargar las citas.</p>';
                }
            }

            function displayCitas(citas) {
                dom.citasContainer.innerHTML = '';
                if (citas.length === 0) {
                    dom.citasContainer.innerHTML = '<p class="text-gray-400">No hay citas programadas para hoy.</p>';
                    return;
                }

                citas.forEach(cita => {
                    const card = document.createElement('div');
                    card.className = 'bg-gray-800 p-4 rounded-lg shadow-lg flex justify-between items-center';
                    const fechaHora = new Date(`${cita.fecha_cita}T${cita.hora_cita}`);

                    const notasHTML = cita.notas
                        ? `<p class="text-sm text-yellow-300 mt-2"><i class="fas fa-sticky-note mr-2"></i><strong>Notas:</strong> ${cita.notas}</p>`
                        : '';

                    card.innerHTML = `
                        <div class="flex-grow pr-4">
                            <p class="font-bold text-lg text-white">${cita.placa}</p>
                            <p class="text-sm text-gray-300">${cita.nombre_cliente || 'Cliente no especificado'}</p>
                            <p class="text-sm text-gray-400">Hora: ${fechaHora.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                            <p class="text-sm text-gray-400">Servicio: ${cita.servicio_base || 'No especificado'}</p>
                            ${notasHTML}
                        </div>
                        <button data-cita='${JSON.stringify(cita)}' class="iniciar-servicio-btn bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition self-center">
                            Iniciar Servicio <i class="fas fa-play ml-2"></i>
                        </button>
                    `;
                    dom.citasContainer.appendChild(card);
                });
            }

            dom.citasContainer.addEventListener('click', (e) => {
                const button = e.target.closest('.iniciar-servicio-btn');
                if (button) {
                    const citaData = JSON.parse(button.dataset.cita);
                    sessionStorage.setItem('citaParaServicio', JSON.stringify(citaData));
                    window.location.href = 'registrar_servicio.php';
                }
            });

            fetchCitasDelDia();
        });
    </script>
</body>
</html>
