<?php
require_once 'conexion.php';

// Verificar el estado actual de la caja
$stmt = $conexion->prepare("SELECT * FROM caja WHERE fecha_cierre IS NULL ORDER BY fecha_apertura DESC LIMIT 1");
$stmt->execute();
$caja_abierta = $stmt->fetch(PDO::FETCH_ASSOC);

$monto_inicial = $caja_abierta ? $caja_abierta['monto_inicial'] : 0;
$fecha_apertura = $caja_abierta ? new DateTime($caja_abierta['fecha_apertura']) : null;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Caja</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-900 text-white">

<?php 
$currentPage = 'gestion_caja';
include 'menu.php'; 
?>

<main id="main-content" class="lg:ml-64 transition-all duration-300 ease-in-out">
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-white mb-6">Gestión de Caja</h1>

        <div id="caja-status" class="bg-gray-800 p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-bold mb-4">Estado Actual</h2>
            <?php if ($caja_abierta): ?>
                <p class="text-green-400"><i class="fas fa-check-circle mr-2"></i>Caja Abierta</p>
                <p><strong>Monto Inicial:</strong> <span id="monto-inicial-display">₡<?= htmlspecialchars(number_format($monto_inicial, 2, '.', ',')) ?></span></p>
                <p><strong>Fecha de Apertura:</strong> <?= htmlspecialchars($fecha_apertura->format('d/m/Y H:i:s')) ?></p>
            <?php else: ?>
                <p class="text-red-400"><i class="fas fa-times-circle mr-2"></i>Caja Cerrada</p>
                <p>No hay una sesión de caja activa. Debe abrir la caja para registrar ventas.</p>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Formulario de Apertura de Caja -->
            <div id="apertura-caja-form" class="bg-gray-800 p-6 rounded-lg shadow-lg <?= $caja_abierta ? 'hidden' : '' ?>">
                <h2 class="text-2xl font-bold mb-4">Abrir Caja</h2>
                <form id="form-abrir">
                    <div class="mb-4">
                        <label for="monto_inicial" class="block text-sm font-bold mb-2">Monto Inicial (Efectivo)</label>
                        <input type="number" id="monto_inicial" name="monto_inicial" class="w-full p-2 bg-gray-700 rounded border border-gray-600 focus:outline-none focus:border-blue-500" step="0.01" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Abrir Caja</button>
                </form>
            </div>

            <!-- Botón y Modal de Cierre de Caja -->
            <div id="cierre-caja-container" class="bg-gray-800 p-6 rounded-lg shadow-lg <?= !$caja_abierta ? 'hidden' : '' ?>">
                <h2 class="text-2xl font-bold mb-4">Cerrar Caja</h2>
                <p class="mb-4">Realiza el cierre de caja para calcular las ventas del día.</p>
                <button id="btn-mostrar-cierre" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Iniciar Cierre de Caja</button>
            </div>
        </div>
    </div>
</main>

<!-- Modal para Cierre de Caja -->
<div id="modal-cierre" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-gray-800 p-8 rounded-lg shadow-2xl w-full max-w-md mx-4">
        <h2 class="text-2xl font-bold mb-4">Resumen de Cierre</h2>
        <div id="resumen-cierre-content" class="space-y-3 mb-6">
            <!-- El resumen se cargará aquí -->
            <p class="text-center">Cargando resumen...</p>
        </div>
        <form id="form-cerrar">
             <div class="mb-4">
                <label for="monto_final_real" class="block text-sm font-bold mb-2">Monto Real en Caja (Efectivo Contado)</label>
                <input type="text" inputmode="decimal" id="monto_final_real" name="monto_real_en_caja" class="w-full p-2 bg-gray-700 rounded border border-gray-600" required>
            </div>
            <div class="mb-6">
                <label for="notas_cierre" class="block text-sm font-bold mb-2">Notas Adicionales</label>
                <textarea id="notas_cierre" name="notas" rows="3" class="w-full p-2 bg-gray-700 rounded border border-gray-600"></textarea>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" id="btn-cancelar-cierre" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancelar</button>
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Confirmar y Cerrar Caja</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
        const montoFinalInput = document.getElementById('monto_final_real');
    if (montoFinalInput) {
        montoFinalInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value) {
                e.target.value = new Intl.NumberFormat('es-CR').format(value);
            } else {
                e.target.value = '';
            }
        });
    }

    const formAbrir = document.getElementById('form-abrir');
    const formCerrar = document.getElementById('form-cerrar');
        const btnMostrarCierre = document.getElementById('btn-mostrar-cierre');
    const btnCancelarCierre = document.getElementById('btn-cancelar-cierre');
    const modalCierre = document.getElementById('modal-cierre');
        const resumenContent = document.getElementById('resumen-cierre-content');

    const formatCRC = (value) => {
        return new Intl.NumberFormat('es-CR', {
            style: 'currency',
            currency: 'CRC'
        }).format(value);
    };

    // Abrir caja
    formAbrir.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formAbrir);
        const response = await fetch('api_caja.php?accion=abrir', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            alert('Caja abierta exitosamente');
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    });

    // Mostrar modal de cierre
        btnMostrarCierre.addEventListener('click', async () => {
        modalCierre.classList.remove('hidden');
        resumenContent.innerHTML = '<p class="text-center">Cargando resumen...</p>';
        
        try {
            const response = await fetch('api_caja.php?accion=get_resumen');
            const result = await response.json();

            if (result.success) {
                const resumen = result.data;
                resumenContent.innerHTML = `
                    <p><strong>Monto Inicial:</strong> ${formatCRC(resumen.monto_inicial)}</p>
                    <p><strong>Total de Ventas (Efectivo):</strong> ${formatCRC(resumen.total_ventas_efectivo)}</p>
                    <p><strong>Total de Ventas (Tarjeta):</strong> ${formatCRC(resumen.total_ventas_tarjeta)}</p>
                    <p><strong>Total General de Ventas:</strong> ${formatCRC(resumen.total_ventas_general)}</p>
                    <p><strong>Total de Servicios:</strong> ${resumen.total_servicios}</p>
                    <hr class="border-gray-600 my-2">
                    <p class="font-bold"><strong>Monto Esperado en Caja:</strong> ${formatCRC(resumen.monto_esperado)}</p>
                `;
            } else {
                resumenContent.innerHTML = `<p class="text-red-400">Error al cargar el resumen: ${result.message}</p>`;
            }
        } catch (error) {
            console.error('Error al obtener el resumen de caja:', error);
            resumenContent.innerHTML = `<p class="text-red-400">Error de conexión al intentar cargar el resumen.</p>`;
        }
    });

    // Cancelar cierre
    btnCancelarCierre.addEventListener('click', () => {
        modalCierre.classList.add('hidden');
    });

    // Confirmar y cerrar caja
        formCerrar.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const montoFinalInput = document.getElementById('monto_final_real');
        const notasInput = document.getElementById('notas_cierre');

        const montoSinFormato = montoFinalInput.value.replace(/[^0-9]/g, '');

        const formData = new FormData();
        formData.append('monto_real_en_caja', montoSinFormato);
        formData.append('notas', notasInput.value);

        const response = await fetch('api_caja.php?accion=cerrar', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            alert('Caja cerrada exitosamente');
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    });
});
</script>

</body>
</html>
