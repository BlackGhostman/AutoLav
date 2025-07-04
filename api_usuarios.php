<?php
require_once 'proteger_api.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión
require 'conexion.php';

// --- Verificación de seguridad (Rol de Admin) ---
if ($_SESSION['user_role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado. Se requiere permiso de Administrador.']);
    exit();
}

$action = $_REQUEST['action'] ?? null;

switch ($action) {
    case 'read':
        readUsers($conexion);
        break;
    case 'create':
        createUser($conexion);
        break;
    case 'update':
        updateUser($conexion);
        break;
    case 'delete':
        deleteUser($conexion);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        break;
}

function readUsers($db) {
    try {
        $stmt = $db->query("SELECT id, nombre, rol FROM usuarios ORDER BY id");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'usuarios' => $users]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al leer usuarios: ' . $e->getMessage()]);
    }
}

function createUser($db) {
    $nombre = $_POST['nombre'] ?? null;
    $pin = $_POST['pin'] ?? null;
    $rol = $_POST['rol'] ?? null;

    if (empty($nombre) || empty($pin) || empty($rol)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos.']);
        return;
    }

    if (!preg_match('/^\d{6}$/', $pin)) {
        echo json_encode(['success' => false, 'message' => 'El PIN debe ser de 6 dígitos numéricos.']);
        return;
    }

    try {
        $hashed_pin = password_hash($pin, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO usuarios (nombre, pin, rol) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $hashed_pin, $rol]);
        echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al crear usuario: ' . $e->getMessage()]);
    }
}

function updateUser($db) {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $pin = $_POST['pin'] ?? null;
    $rol = $_POST['rol'] ?? null;

    if (empty($id) || empty($nombre) || empty($rol)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos para actualizar.']);
        return;
    }

    try {
        if (!empty($pin)) {
            // Si se proporciona un nuevo PIN, se actualiza
            if (!preg_match('/^\d{6}$/', $pin)) {
                echo json_encode(['success' => false, 'message' => 'El nuevo PIN debe ser de 6 dígitos numéricos.']);
                return;
            }
            $hashed_pin = password_hash($pin, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, pin = ?, rol = ? WHERE id = ?");
            $stmt->execute([$nombre, $hashed_pin, $rol, $id]);
        } else {
            // Si no se proporciona PIN, se actualiza sin cambiarlo
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, rol = ? WHERE id = ?");
            $stmt->execute([$nombre, $rol, $id]);
        }
        echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario: ' . $e->getMessage()]);
    }
}

function deleteUser($db) {
    $id = $_POST['id'] ?? null;

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó ID para eliminar.']);
        return;
    }

    // Evitar que el admin se elimine a sí mismo
    if ($id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propia cuenta de administrador.']);
        return;
    }

    try {
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar usuario: ' . $e->getMessage()]);
    }
}
?>
