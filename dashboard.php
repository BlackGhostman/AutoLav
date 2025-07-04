<?php 
$page_title = 'Dashboard - Lavacar';
require_once 'header.php';
?>

<div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-12 sm:mt-0">
    <h1 class="text-2xl font-bold">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p class="mt-2">Has iniciado sesión como <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong>.</p>
    <p class="mt-2">Selecciona una opción del menú para comenzar.</p>
</div>

<?php 
require_once 'footer.php';
?>
