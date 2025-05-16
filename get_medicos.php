<?php
include 'conexion.php';

if (isset($_POST['especialidad_id'])) {
    $esp_id = (int)$_POST['especialidad_id'];

    $stmt = $pdo->prepare("SELECT m.id, u.nombre FROM medicos m JOIN usuarios u ON m.usuario_id = u.id WHERE m.especialidad_id = ?");
    $stmt->execute([$esp_id]);

    if ($stmt->rowCount()) {
        echo '<option value="">--Seleccione--</option>';
        foreach ($stmt as $m) {
            echo '<option value="'.$m['id'].'">'.htmlspecialchars($m['nombre']).'</option>';
        }
    } else {
        echo '<option value="">No hay médicos disponibles</option>';
    }
}
