<?php
require 'conexion.php';

echo "<pre>";

// --- USUARIOS DE EJEMPLO ---
$usuarios = [
    [
        'nombre' => 'Administrador General',
        'pin'    => '111111', 
        'rol'    => 'Admin'
    ],
    [
        'nombre' => 'Cajero Turno Mañana',
        'pin'    => '222222', 
        'rol'    => 'caja'
    ],
    [
        'nombre' => 'Lavador Experto',
        'pin'    => '333333', 
        'rol'    => 'lavado'
    ]
];


echo "Iniciando inserción de usuarios...\n";

foreach ($usuarios as $usuario) {
    try {
        // Verificar si el usuario ya existe por nombre
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE nombre = ?");
        $stmt->execute([$usuario['nombre']]);
        if ($stmt->fetch()) {
            echo "Usuario '{$usuario['nombre']}' ya existe. Saltando...\n";
            continue;
        }

        // Hashear el PIN antes de insertarlo
        $hashed_pin = password_hash($usuario['pin'], PASSWORD_DEFAULT);

        // Preparar la consulta SQL
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, pin, rol) VALUES (?, ?, ?)");
        
        // Ejecutar la consulta
        $stmt->execute([
            $usuario['nombre'],
            $hashed_pin,
            $usuario['rol']
        ]);

        echo "Usuario '{$usuario['nombre']}' insertado correctamente.\n";

    } catch (PDOException $e) {
        echo "Error al insertar a '{$usuario['nombre']}': " . $e->getMessage() . "\n";
    }
}

echo "\nProceso de inserción finalizado.\n";
echo "</pre>";

?>
