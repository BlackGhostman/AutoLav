<?php
header('Content-Type: application/json');

// --- Configuración de la Base de Datos ---
$host = 'localhost';
$db   = 'autolavado'; // Asume que la base de datos se llama 'autolavado'
$user = 'root';      // Usuario por defecto de XAMPP
$pass = '';          // Contraseña por defecto de XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'mensaje' => 'Error de conexión a la base de datos.']);
    // En un entorno de producción, registrarías este error en lugar de mostrarlo.
    // throw new \PDOException($e->getMessage(), (int)$e->getCode());
    exit();
}

// --- Lógica de la API ---
$accion = $_REQUEST['accion'] ?? null;

switch ($accion) {
    case 'iniciar':
        $placa = strtoupper(trim($_POST['placa'] ?? ''));
        if (empty($placa)) {
            echo json_encode(['success' => false, 'mensaje' => 'La placa no puede estar vacía.']);
            exit();
        }

        // Verificar si ya existe una orden activa para esta placa
        $stmt = $pdo->prepare("SELECT id FROM ordenes WHERE placa = ? AND estado = 'activo'");
        $stmt->execute([$placa]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'mensaje' => 'Ya existe una orden activa para esta placa.']);
            exit();
        }

        $sql = "INSERT INTO ordenes (placa, hora_inicio, estado) VALUES (?, NOW(), 'activo')";
        $stmt = $pdo->prepare($sql);
        $exito = $stmt->execute([$placa]);
        echo json_encode(['success' => $exito]);
        break;

    case 'finalizar':
        $id = $_POST['id'] ?? 0;
        $sql = "UPDATE ordenes SET hora_fin = NOW(), estado = 'finalizado' WHERE id = ? AND estado = 'activo'";
        $stmt = $pdo->prepare($sql);
        $exito = $stmt->execute([$id]);
        echo json_encode(['success' => $exito]);
        break;

    case 'listar':
        $stmt = $pdo->query("SELECT id, placa, hora_inicio FROM ordenes WHERE estado = 'activo' ORDER BY hora_inicio ASC");
        $ordenes = $stmt->fetchAll();
        echo json_encode($ordenes);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Acción no válida.']);
        break;
}

// --- Creación de la tabla (si no existe) ---
// Este código se puede ejecutar una vez para configurar la base de datos.
/*
CREATE TABLE IF NOT EXISTS `ordenes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `placa` varchar(20) NOT NULL,
  `hora_inicio` datetime NOT NULL,
  `hora_fin` datetime DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'activo', -- 'activo', 'finalizado'
  PRIMARY KEY (`id`),
  KEY `placa` (`placa`),
  KEY `estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/
?>
