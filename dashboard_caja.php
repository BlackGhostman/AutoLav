<?php
// dashboard_caja.php - Vista para Caja
// La variable $data es proporcionada por dashboard.php
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Card: Servicios por Facturar -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="bg-red-500 p-3 rounded-full text-white">
                <i class="fas fa-file-invoice fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Servicios por Facturar</p>
                <h3 class="text-2xl font-bold"><?php echo $data['servicios_por_facturar'] ?? 0; ?></h3>
            </div>
        </div>
    </div>

    <!-- Card: Caja del Día -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="bg-green-500 p-3 rounded-full text-white">
                <i class="fas fa-cash-register fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Caja del Día</p>
                <h3 class="text-2xl font-bold">₡<?php echo number_format($data['caja_dia'] ?? 0, 2); ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Gráficos -->
<div class="mt-8 bg-white p-6 rounded-lg shadow-lg">
    <h4 class="text-xl font-bold mb-4">Flujo de Caja</h4>
    <div class="relative h-96 max-w-lg mx-auto">
    <canvas id="cajaChart"></canvas>
</div>
</div>
