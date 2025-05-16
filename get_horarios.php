<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['medico_id'], $_POST['fecha'])) {
    $medico_id = (int)$_POST['medico_id'];
    $fecha = $_POST['fecha'];

    // Horarios fijos por ejemplo de 08:00 a 17:00 cada hora
    $horarios = [
        '08:00', '09:00', '10:00', '11:00', '12:00',
        '13:00', '14:00', '15:00', '16:00', '17:00'
    ];

    // Consultar citas ya agendadas para ese médico y fecha
    $stmt = $pdo->prepare("SELECT hora FROM citas WHERE medico_id = ? AND fecha = ?");
    $stmt->execute([$medico_id, $fecha]);
    $ocupados = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Mostrar horarios disponibles
    foreach ($horarios as $hora) {
        if (!in_array($hora, $ocupados)) {
            echo '<option value="'.$hora.'">'.$hora.'</option>';
        }
    }
}
?>
