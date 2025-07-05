<?php 
$page_title = 'Dashboard - Lavacar';
require_once 'header.php';
?>

<main id="main-content" class="lg:ml-64 transition-all duration-300 ease-in-out">
    <div class="p-4 mt-14">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
            <h1 class="text-2xl font-bold">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p class="mt-2">Has iniciado sesión como <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong>.</p>
            <p class="mt-2">Selecciona una opción del menú para comenzar.</p>
        </div>
    </div>
</main>

<?php 
require_once 'footer.php';
?>
