<?php
require_once 'proteger_pagina.php';

// Incluir el menú que ahora es parte del header
include 'menu.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- El título se puede definir en cada página antes de incluir el header -->
    <title><?php echo $page_title ?? 'AutoSpa Blue Line'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Puedes agregar aquí más estilos globales si es necesario */
    </style>
</head>
<body class="bg-gray-100">

    <!-- Botón para mostrar/ocultar menú en móviles -->
    <button id="sidebar-toggle" class="fixed top-4 left-4 z-50 p-2 rounded-md bg-gray-800 text-white sm:hidden" aria-controls="sidebar" aria-expanded="false">
        <span class="sr-only">Abrir menú</span>
        <i class="fas fa-bars"></i>
    </button>

    <!-- Contenido Principal -->
    <main class="p-4 sm:ml-64 transition-all duration-300">
        <!-- El contenido específico de cada página irá aquí -->
