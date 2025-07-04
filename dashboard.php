<?php 
require_once 'proteger_pagina.php';

// Incluir el menú
include 'menu.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lavacar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100">

    <div class="p-4 sm:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
            <h1 class="text-2xl font-bold">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p class="mt-2">Has iniciado sesión como <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong>.</p>
            <p class="mt-2">Selecciona una opción del menú para comenzar.</p>
        </div>
    </div>

</body>
</html>
