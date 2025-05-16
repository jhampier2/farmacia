<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 3) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = $_SESSION['usuario_id'];
    $medico_id = (int)$_POST['medico_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    // Validar que la cita no exista
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE medico_id = ? AND fecha = ? AND hora = ?");
    $stmt->execute([$medico_id, $fecha, $hora]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        die("Error: Esta cita ya está ocupada.");
    }

    // Insertar cita
    $stmt = $pdo->prepare("INSERT INTO citas (paciente_id, medico_id, fecha, hora) VALUES (?, ?, ?, ?)");
    $stmt->execute([$paciente_id, $medico_id, $fecha, $hora]);

    echo "Cita agendada exitosamente.";
    echo '<br><a href="panel_paciente.php">Volver al panel</a>';
    exit;
}

header("Location: panel_paciente.php");
exit;
?>