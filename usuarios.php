<?php 
require_once 'proteger_pagina.php'; 
// Verificación adicional para rol de Admin
if ($_SESSION['user_role'] !== 'Admin') {
    $_SESSION['error_message'] = 'No tienes permiso para acceder a esta página.';
    header('Location: dashboard.php');
    exit();
}

$currentPage = 'usuarios';
include 'header.php';
?>

    <!-- El menú lateral ya está incluido con menu.php -->

    <!-- Contenido principal -->
    <div class="p-4 lg:ml-64 transition-all duration-300 ease-in-out">
        <div class="p-4 mt-14">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Gestión de Usuarios</h1>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="flex justify-end mb-4">
                    <button id="btn-nuevo-usuario" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-plus mr-2"></i>Crear Nuevo Usuario
                    </button>
                </div>
                
                <!-- Tabla de Usuarios -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="w-1/6 text-left py-3 px-4 uppercase font-semibold text-sm">ID</th>
                                <th class="w-2/6 text-left py-3 px-4 uppercase font-semibold text-sm">Nombre</th>
                                <th class="w-2/6 text-left py-3 px-4 uppercase font-semibold text-sm">Rol</th>
                                <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-usuarios-body" class="text-gray-700">
                            <!-- Las filas se cargarán aquí con JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Crear/Editar Usuario (inicialmente oculto) -->
    <div id="usuario-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Crear Usuario</h3>
                <form id="usuario-form" class="mt-2 px-7 py-3">
                    <input type="hidden" id="usuario-id" name="id">
                    <div class="mb-4">
                        <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2 text-left">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label for="pin" class="block text-gray-700 text-sm font-bold mb-2 text-left">PIN (6 dígitos):</label>
                        <input type="password" id="pin" name="pin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" pattern="\d{6}" title="El PIN debe contener 6 dígitos numéricos.">
                        <small id="pin-info" class="text-gray-600">Dejar en blanco para no cambiar el PIN al editar.</small>
                    </div>
                    <div class="mb-4">
                        <label for="rol" class="block text-gray-700 text-sm font-bold mb-2 text-left">Rol:</label>
                        <select id="rol" name="rol" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="Admin">Admin</option>
                            <option value="caja">Caja</option>
                            <option value="lavado">Lavado</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-end p-6 border-t border-solid border-blueGray-200 rounded-b">
                        <button type="button" id="btn-cancelar" class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-500 text-white active:bg-blue-600 font-bold uppercase text-sm px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
// Aquí irá nuestro código JavaScript para el CRUD
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('usuario-modal');
    const btnNuevo = document.getElementById('btn-nuevo-usuario');
    const btnCancelar = document.getElementById('btn-cancelar');
    const form = document.getElementById('usuario-form');
    const modalTitle = document.getElementById('modal-title');
    const pinInfo = document.getElementById('pin-info');

    // Cargar usuarios al iniciar
    cargarUsuarios();

    // Abrir modal para crear
    btnNuevo.addEventListener('click', () => {
        form.reset();
        document.getElementById('usuario-id').value = '';
        modalTitle.textContent = 'Crear Nuevo Usuario';
        pinInfo.classList.add('hidden');
        document.getElementById('pin').setAttribute('required', 'required');
        modal.classList.remove('hidden');
    });

    // Cerrar modal
    btnCancelar.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Función para cargar y mostrar usuarios
    function cargarUsuarios() {
        fetch('api_usuarios.php?action=read')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('tabla-usuarios-body');
                tbody.innerHTML = ''; // Limpiar tabla
                if (data.success) {
                    data.usuarios.forEach(user => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="text-left py-3 px-4">${user.id}</td>
                            <td class="text-left py-3 px-4">${user.nombre}</td>
                            <td class="text-left py-3 px-4">${user.rol}</td>
                            <td class="text-left py-3 px-4">
                                <button class="btn-editar bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded" data-id="${user.id}" data-nombre="${user.nombre}" data-rol="${user.rol}"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn-eliminar bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded" data-id="${user.id}"><i class="fas fa-trash"></i></button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            });
    }

    // Evento para enviar el formulario (Crear/Editar)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const id = document.getElementById('usuario-id').value;
        const action = id ? 'update' : 'create';
        formData.append('action', action);

        fetch('api_usuarios.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    modal.classList.add('hidden');
                    cargarUsuarios();
                } else {
                    alert('Error: ' + data.message);
                }
            });
    });

    // Eventos para botones de la tabla (Editar/Eliminar)
    document.getElementById('tabla-usuarios-body').addEventListener('click', function(e) {
        const target = e.target.closest('button');
        if (!target) return;

        const id = target.dataset.id;

        if (target.classList.contains('btn-editar')) {
            // Acción de editar
            form.reset();
            document.getElementById('usuario-id').value = id;
            document.getElementById('nombre').value = target.dataset.nombre;
            document.getElementById('rol').value = target.dataset.rol;
            modalTitle.textContent = 'Editar Usuario';
            pinInfo.classList.remove('hidden');
            document.getElementById('pin').removeAttribute('required');
            modal.classList.remove('hidden');
        }

        if (target.classList.contains('btn-eliminar')) {
            // Acción de eliminar
            if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);

                fetch('api_usuarios.php', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            cargarUsuarios();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
            }
        }
    });
});
</script>

<?php include 'footer.php'; ?>
