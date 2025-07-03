<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$estado = $data['estado'] ?? null;

if (!$id || !$estado) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos (ID o estado).']);
    exit;
}

$sql = "UPDATE citas SET estado = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("si", $estado, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Estado de la cita actualizado.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
