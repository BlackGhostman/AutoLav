<?php
// Este archivo asume que ya hay una sesión iniciada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definimos los items del menú para cada rol
$rol = $_SESSION['user_role'] ?? '';

// Menú base para todos
$menu_final = [
    'Dashboard' => 'dashboard.php',
];

// Permisos por rol
$permisos = [
    'Admin' => [
        'Registrar Servicio' => 'registrar_servicio.php',
        'Citas' => 'citas.php',
        'Facturas' => 'facturacion.php',
        'Gestión de Caja' => 'gestion_caja.php',
        'Citas del Día' => 'citas_dia.php',
        'Servicios en Proceso' => 'servicios_en_proceso.php',
        'Reportes' => 'reportes.php',
        'Usuarios' => 'usuarios.php',
    ],
    'caja' => [
        'Registrar Servicio' => 'registrar_servicio.php',
        'Citas' => 'citas.php',
        'Facturas' => 'facturacion.php',
        'Gestión de Caja' => 'gestion_caja.php',
    ],
    'lavado' => [
        'Registrar Servicio' => 'registrar_servicio.php',
        'Citas del Día' => 'citas_dia.php',
        'Servicios en Proceso' => 'servicios_en_proceso.php',
    ]
];

// Si el rol existe en los permisos, se combina con el menú base
if (array_key_exists($rol, $permisos)) {
    $menu_final = array_merge($menu_final, $permisos[$rol]);
}

?>

<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    <div class="flex items-center justify-between">
      <div class="flex items-center justify-start">
        <a href="dashboard.php" class="flex ml-2 md:mr-24">
          <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">LavaCar Deluxe</span>
        </a>
      </div>
      <div class="flex items-center">
          <div class="flex items-center ml-3">
            <div>
              <span class="block text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Invitado'); ?></span>
              <span class="block text-xs text-gray-500 truncate dark:text-gray-400"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Sin rol'); ?></span>
            </div>
          </div>
        </div>
    </div>
  </div>
</nav>

<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
      <ul class="space-y-2 font-medium">
         <?php
            // Renderizar el menú final
            foreach ($menu_final as $label => $url) {
                echo '<li><a href="' . $url . '" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"><span class="ml-3">' . htmlspecialchars($label) . '</span></a></li>';
            }

            // Opción de Salir siempre al final, si hay sesión
            if (isset($_SESSION['user_id'])) {
                echo '<li class="pt-4 mt-4 space-y-2 border-t border-gray-200 dark:border-gray-700"><a href="logout.php" class="flex items-center p-2 text-red-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group"><span class="ml-3">Cerrar Sesión</span></a></li>';
            }
         ?>
      </ul>
   </div>
</aside>
