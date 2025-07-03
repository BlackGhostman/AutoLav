<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó el ID de la cita.']);
    exit;
}

$sql = "UPDATE citas SET estado = 'Cancelada' WHERE id = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cita cancelada correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al cancelar la cita: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
