<?php
// dashboard_Admin.php - Vista para el Administrador
// La variable $data es proporcionada por dashboard.php
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Card: Ingresos del Día -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="bg-green-500 p-3 rounded-full text-white">
                <i class="fas fa-dollar-sign fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Ingresos del Día</p>
                <h3 class="text-2xl font-bold">₡<?php echo number_format($data['ingresos_hoy'] ?? 0, 2); ?></h3>
            </div>
        </div>
    </div>

    <!-- Card: Servicios de Hoy -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="bg-blue-500 p-3 rounded-full text-white">
                <i class="fas fa-car fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">Servicios de Hoy</p>
                <h3 class="text-2xl font-bold"><?php echo $data['servicios_hoy'] ?? 0; ?></h3>
            </div>
        </div>
    </div>

    <!-- Card: Servicios en Proceso -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="bg-yellow-500 p-3 rounded-full text-white">
                <i class="fas fa-tasks fa-2x"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500">En Proceso</p>
                <h3 class="text-2xl font-bold"><?php echo $data['servicios_proceso'] ?? 0; ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Gráficos -->
<div class="mt-8 bg-white p-6 rounded-lg shadow-lg">
    <h4 class="text-xl font-bold mb-4">Rendimiento Semanal</h4>
    <div class="relative h-96">
    <canvas id="adminChart"></canvas>
</div>
</div>
