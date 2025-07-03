<?php
// Default currentPage to avoid errors if not set
if (!isset($currentPage)) {
    $currentPage = '';
}

$menuItems = [
    'monitor' => ['href' => 'index.php', 'icon' => 'fas fa-tv', 'text' => 'Monitor'],
    'registrar_servicio' => ['href' => 'registrar_servicio.php', 'icon' => 'fas fa-plus-circle', 'text' => 'Registrar Servicio'],
    'citas' => ['href' => 'citas.php', 'icon' => 'fas fa-calendar-alt', 'text' => 'Citas'],
    'citas_del_dia' => ['href' => 'citas_del_dia.php', 'icon' => 'fas fa-calendar-day', 'text' => 'Citas del Día'],
    'servicios_en_proceso' => ['href' => 'servicios_en_proceso.php', 'icon' => 'fas fa-tasks', 'text' => 'Servicios en Proceso'],
    'facturacion' => ['href' => 'facturacion.php', 'icon' => 'fas fa-file-invoice-dollar', 'text' => 'Facturación']
];
?>
<!-- Vertical Sidebar -->
<aside id="sidebar" class="bg-gray-800 text-white w-64 min-h-screen p-4 fixed top-0 left-0 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 shadow-xl">
    <div class="text-center mb-10">
        <a href="index.php">
            <img src="img/5125430911406550677.jpg" alt="AutoSpa Blue Line" class="mx-auto mb-4" width="150">
        </a>
    </div>
    <nav>
        <?php foreach ($menuItems as $key => $item): ?>
            <a href="<?php echo htmlspecialchars($item['href']); ?>" 
               class="flex items-center py-2.5 px-4 my-2 rounded transition duration-200 <?php echo ($currentPage === $key) ? 'bg-blue-700' : 'hover:bg-blue-700'; ?>">
                <i class="<?php echo htmlspecialchars($item['icon']); ?> mr-3 w-6 text-center"></i> 
                <span><?php echo htmlspecialchars($item['text']); ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>

<!-- Hamburger Button (for mobile) -->
<button id="sidebar-toggle" class="lg:hidden fixed top-4 left-4 z-50 text-white p-2 bg-gray-800 rounded-md focus:outline-none">
    <svg id="hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
    <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
</button>

<!-- Sidebar Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const hamburgerIcon = document.getElementById('hamburger-icon');
        const closeIcon = document.getElementById('close-icon');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                hamburgerIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });
        }
    });
</script>
