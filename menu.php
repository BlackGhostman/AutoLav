<?php
// Este archivo asume que ya hay una sesión iniciada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definimos los items del menú para cada rol
$rol = $_SESSION['user_role'] ?? '';

// --- CONFIGURACIÓN DEL MENÚ ---
// 1. Definir todos los posibles items del menú con sus rutas e iconos de Font Awesome
$todos_los_items = [
    'Dashboard'             => ['href' => 'dashboard.php', 'icon' => 'fas fa-tv'],
    'Registrar Servicio'    => ['href' => 'registrar_servicio.php', 'icon' => 'fas fa-plus-circle'],
    'Citas'                 => ['href' => 'citas.php', 'icon' => 'fas fa-calendar-alt'],
    'Facturas'              => ['href' => 'facturacion.php', 'icon' => 'fas fa-file-invoice-dollar'],
    'Facturas Electrónicas' => ['href' => 'facturas_electronicas.php', 'icon' => 'fas fa-paper-plane'],
    'Gestión de Caja'       => ['href' => 'gestion_caja.php', 'icon' => 'fas fa-cash-register'],
    'Citas del Día'         => ['href' => 'citas_del_dia.php', 'icon' => 'fas fa-calendar-day'],
    'Servicios en Proceso'  => ['href' => 'servicios_en_proceso.php', 'icon' => 'fas fa-tasks'],
    'Reportes'              => ['href' => 'reportes.php', 'icon' => 'fas fa-chart-line'],
    'Usuarios'              => ['href' => 'usuarios.php', 'icon' => 'fas fa-users'],
];

// 2. Definir los permisos para cada rol (los textos deben coincidir con las claves de $todos_los_items)
$permisos = [
    'Admin'  => array_keys($todos_los_items), // Admin tiene acceso a todo
    'caja'   => ['Dashboard', 'Registrar Servicio', 'Citas', 'Facturas', 'Facturas Electrónicas', 'Gestión de Caja'],
    'lavado' => ['Dashboard', 'Registrar Servicio', 'Citas del Día', 'Servicios en Proceso']
];

// 3. Construir el menú final para el rol actual
$menu_final = [];
if (isset($permisos[$rol])) {
    foreach ($permisos[$rol] as $item_label) {
        if (isset($todos_los_items[$item_label])) {
            $menu_final[$item_label] = $todos_los_items[$item_label];
        }
    }
}

// 4. Determinar la página actual para el estado activo
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Vertical Sidebar -->
<aside id="sidebar" class="bg-gray-800 text-white w-64 min-h-screen p-4 fixed top-0 left-0 transform -translate-x-full sm:translate-x-0 transition-transform duration-300 ease-in-out z-40 shadow-xl">
    <div class="text-center mb-10">
        <a href="dashboard.php">
            <!-- NOTA: Cambia 'img/logo.png' por la ruta real de tu logo -->
            <img src="img/logo.png" alt="Logo Lavacar" class="mx-auto mb-4 w-32 h-auto" onerror="this.style.display='none'; this.nextSibling.style.display='block';">
            <div style="display:none;" class="text-2xl font-bold">Lavacar</div>
        </a>
        <h2 class="text-xl font-semibold mt-4"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Invitado'); ?></h2>
        <p class="text-sm text-gray-400"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Sin rol'); ?></p>
    </div>
    <nav class="flex flex-col justify-between" style="height: calc(100vh - 200px);">
        <ul>
            <?php foreach ($menu_final as $label => $item): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>" 
                       class="menu-link flex items-center py-2.5 px-4 my-1 rounded-lg transition duration-200 <?php echo ($currentPage === $item['href']) ? 'bg-blue-700' : 'hover:bg-gray-700'; ?>">
                        <i class="<?php echo htmlspecialchars($item['icon']); ?> mr-3 w-5 text-center"></i> 
                        <span><?php echo htmlspecialchars($label); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <ul>
            <li>
                <a href="logout.php" 
                   class="menu-link flex items-center py-2.5 px-4 my-1 rounded-lg transition duration-200 text-red-400 hover:bg-red-700 hover:text-white">
                    <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i> 
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<script>
document.querySelectorAll('.menu-link').forEach(function(link) {
    link.addEventListener('click', function() {
        // Solo cerrar en pantallas pequeñas (menor a 640px)
        if (window.innerWidth < 640) {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.remove('open');
            sidebar.style.transform = 'translateX(-100%)';
        }
    });
});
</script>
