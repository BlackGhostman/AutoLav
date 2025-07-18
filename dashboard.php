<?php 
// Activar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$page_title = 'Dashboard - Lavacar';
require_once 'header.php';
?>

<?php
// --- VERIFICACIÓN DE ROL ---
if (!isset($_SESSION['user_role'])) {
    echo "<main id='main-content' class='lg:ml-64 p-4 mt-14'><div class='p-4 border-2 border-gray-200 border-dashed rounded-lg'><p>Error: No se ha identificado el rol del usuario. Por favor, inicie sesión de nuevo.</p></div></main>";
    require_once 'footer.php';
    exit;
}

// --- CARGA DE DATOS ---
require_once 'data_dashboard.php';

// --- SELECCIÓN DE VISTA POR ROL ---
$rol = $_SESSION['user_role'];
$data = [];

// Obtener los datos específicos para el rol
switch ($rol) {
    case 'Admin':
        $data = get_admin_data($conexion);
        break;
    case 'caja':
        $data = get_caja_data($conexion);
        break;
    case 'lavado':
        $data = get_lavado_data($conexion);
        break;
    default:
        echo "<main id='main-content' class='lg:ml-64 p-4 mt-14'><div class='p-4 border-2 border-gray-200 border-dashed rounded-lg'><p>Rol no reconocido.</p></div></main>";
        require_once 'footer.php';
        exit;
}
?>

<main id="main-content" class="lg:ml-64 transition-all duration-300 ease-in-out">
    <div class="p-4 mt-14">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Dashboard: <?php echo htmlspecialchars(ucfirst($rol)); ?></h1>
        <?php
        // --- INCLUSIÓN DE LA VISTA ESPECÍFICA ---
        $view_file = "dashboard_{$rol}.php";
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            echo "<div class='p-4 border-2 border-gray-200 border-dashed rounded-lg'><p>La vista para el rol '{$rol}' no está disponible.</p></div>";
        }
        ?>
    </div>
</main>

<!-- Inclusión de Chart.js y el script de gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/dashboard_charts.js"></script>


<?php 
require_once 'footer.php';
?>
