<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor de Servicios - AutoSpa Blue Line</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .service-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

<?php 
$currentPage = 'monitor';
include 'menu.php'; 
?>

    <!-- Contenido Principal -->
    <main id="main-content" class="lg:ml-64 flex-grow p-4 md:p-8 overflow-auto">
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-gray-800">Monitor de Servicios en Tiempo Real</h1>
            <p class="text-gray-500">Actualizando automáticamente cada 10 segundos. <span id="last-updated" class="text-xs font-mono"></span></p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Columna: En Proceso -->
            <div>
                <h2 class="text-2xl font-bold mb-4 pb-2 border-b-2 border-blue-500">En Lavado</h2>
                <div id="en-proceso-list" class="space-y-4">
                    <!-- Tarjetas de servicios en proceso -->
                </div>
            </div>

            <!-- Columna: Para Facturar -->
            <div>
                <h2 class="text-2xl font-bold mb-4 pb-2 border-b-2 border-green-500">Listos para Cobrar</h2>
                <div id="para-facturar-list" class="space-y-4">
                    <!-- Tarjetas de servicios para facturar -->
                </div>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const enProcesoList = document.getElementById('en-proceso-list');
        const paraFacturarList = document.getElementById('para-facturar-list');
        const lastUpdatedEl = document.getElementById('last-updated');

        function calculateElapsedTime(startTime) {
            const start = new Date(startTime);
            const now = new Date();
            const diff = Math.abs(now - start);
            const minutes = Math.floor(diff / 60000);
            return `${minutes} min`;
        }

        async function fetchAndRenderServices() {
            try {
                const response = await fetch('listar_servicios_monitor.php');
                const result = await response.json();

                // Limpiar listas
                enProcesoList.innerHTML = '';
                paraFacturarList.innerHTML = '';

                if (result.success && result.data.length > 0) {
                    result.data.forEach(servicio => {
                        const card = document.createElement('div');
                        card.className = 'bg-white p-4 rounded-lg shadow-lg service-card';

                        const elapsedTime = calculateElapsedTime(servicio.hora_inicio);

                        let statusBadge;
                        let targetList;

                        if (servicio.nombre_estado === 'En Proceso') {
                            statusBadge = `<span class="text-xs font-semibold bg-blue-500 text-white px-2 py-1 rounded-full">${elapsedTime}</span>`;
                            targetList = enProcesoList;
                        } else { // Para Facturar
                            statusBadge = `<span class="text-xs font-semibold bg-green-500 text-white px-2 py-1 rounded-full">Listo</span>`;
                            targetList = paraFacturarList;
                        }

                        card.innerHTML = `
                            <div class="flex justify-between items-center">
                                <h3 class="text-xl font-bold text-gray-900">${servicio.placa}</h3>
                                ${statusBadge}
                            </div>
                            <p class="text-sm text-gray-600">${servicio.tipo_vehiculo}</p>
                            <p class="text-xs text-gray-500 mt-1">${servicio.nombre_cliente || 'Cliente no especificado'}</p>
                        `;
                        targetList.appendChild(card);
                    });
                }
                
                if (enProcesoList.innerHTML === '') {
                    enProcesoList.innerHTML = '<p class="text-center text-gray-500">Ningún vehículo en proceso.</p>';
                }
                if (paraFacturarList.innerHTML === '') {
                    paraFacturarList.innerHTML = '<p class="text-center text-gray-500">Ningún vehículo listo para cobrar.</p>';
                }

                lastUpdatedEl.textContent = `(Última actualización: ${new Date().toLocaleTimeString('es-CR')})`;

            } catch (error) {
                console.error('Error fetching monitor data:', error);
                enProcesoList.innerHTML = '<p class="text-red-500">Error de conexión.</p>';
                paraFacturarList.innerHTML = '<p class="text-red-500">Error de conexión.</p>';
            }
        }

        // Cargar los servicios al iniciar y luego cada 10 segundos
        fetchAndRenderServices();
        setInterval(fetchAndRenderServices, 10000); // 10000 ms = 10 segundos
    });
    </script>
</body>
</html>
