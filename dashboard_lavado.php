<?php
// dashboard_lavado.php - Vista para Lavado
// La variable $data es proporcionada por dashboard.php
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Card: Vehículos Pendientes -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="bg-blue-500 p-3 rounded-full text-white">
                <i class="fas fa-car-side fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Vehículos en Proceso</p>
                <h3 class="text-2xl font-bold"><?php echo $data['vehiculos_en_proceso'] ?? 0; ?></h3>
            </div>
        </div>
    </div>

    <!-- Card: Servicios Completados Hoy -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="bg-green-500 p-3 rounded-full text-white">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Servicios Completados Hoy</p>
                <h3 class="text-2xl font-bold"><?php echo $data['servicios_completados_hoy'] ?? 0; ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Gráficos -->
<div class="mt-8 bg-white p-6 rounded-lg shadow-lg">
    <h4 class="text-xl font-bold mb-4">Productividad del Día</h4>
    <div class="relative h-96 max-w-md mx-auto">
    <canvas id="lavadoChart"></canvas>
</div>
</div>
