<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['paciente_id'])) {
    header("Location: login_paciente.php");
    exit;
}

$nombre = $_SESSION['paciente_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Panel Paciente</title>
<style>
    body { font-family: Arial,sans-serif; background:#eef2f7; padding:20px; }
    .container { max-width: 800px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 10px #ccc;}
    h1 { text-align:center; color:#333; }
    label { display:block; font-weight:bold; margin-top:15px; }
    select, button, input[type=date], input[type=datetime-local], textarea {
        width: 100%; padding: 10px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px;
        box-sizing: border-box;
    }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #007bff; color: #fff; }
    .logout { float:right; background:#dc3545; color:#fff; padding:8px 15px; border:none; border-radius:5px; cursor:pointer; text-decoration:none; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('#especialidad').change(function(){
        let espId = $(this).val();
        if(espId){
            $.post('get_medicos.php', {especialidad_id: espId}, function(data){
                $('#medico').html(data);
                $('#medico').prop('disabled', false);
                $('#fecha').val('');
                $('#hora').html('<option value="">--Seleccione primero fecha--</option>');
                $('#hora').prop('disabled', true);
                $('#btn_agendar').prop('disabled', true);
            });
        } else {
            $('#medico').html('<option value="">--Seleccione especialidad primero--</option>').prop('disabled', true);
            $('#fecha').val('');
            $('#hora').html('<option value="">--Seleccione primero fecha--</option>').prop('disabled', true);
            $('#btn_agendar').prop('disabled', true);
        }
    });

    $('#medico').change(function(){
        let medicoId = $(this).val();
        if(medicoId){
            $('#fecha').prop('disabled', false);
            $('#fecha').val('');
            $('#hora').html('<option value="">--Seleccione primero fecha--</option>').prop('disabled', true);
            $('#btn_agendar').prop('disabled', true);
        } else {
            $('#fecha').prop('disabled', true).val('');
            $('#hora').html('<option value="">--Seleccione primero fecha--</option>').prop('disabled', true);
            $('#btn_agendar').prop('disabled', true);
        }
    });

    $('#fecha').change(function(){
        let medicoId = $('#medico').val();
        let fecha = $(this).val();
        if(medicoId && fecha){
            $.post('get_horarios.php', {medico_id: medicoId, fecha: fecha}, function(data){
                $('#hora').html(data);
                $('#hora').prop('disabled', false);
                $('#btn_agendar').prop('disabled', true);
            });
        } else {
            $('#hora').html('<option value="">--Seleccione primero fecha--</option>').prop('disabled', true);
            $('#btn_agendar').prop('disabled', true);
        }
    });

    $('#hora').change(function(){
        let hora = $(this).val();
        if(hora){
            $('#btn_agendar').prop('disabled', false);
        } else {
            $('#btn_agendar').prop('disabled', true);
        }
    });
});
</script>
</head>
<body>
<div class="container">
    <a href="logout_paciente.php" class="logout">Cerrar sesión</a>
    <h1>Bienvenido, <?=htmlspecialchars($nombre)?></h1>

    <form method="POST" action="agendar_cita.php">
        <label for="especialidad">Especialidad:</label>
        <select id="especialidad" name="especialidad_id" required>
            <option value="">--Seleccione--</option>
            <?php
            $especialidades = $pdo->query("SELECT id, nombre FROM especialidades ORDER BY nombre")->fetchAll();
            foreach($especialidades as $e){
                echo "<option value='{$e['id']}'>".htmlspecialchars($e['nombre'])."</option>";
            }
            ?>
        </select>

        <label for="medico">Médico:</label>
        <select id="medico" name="medico_id" disabled required>
            <option value="">--Seleccione especialidad primero--</option>
        </select>

        <label for="fecha">Fecha de cita:</label>
        <input type="date" id="fecha" name="fecha" min="<?=date('Y-m-d')?>" required disabled>

        <label for="hora">Hora:</label>
        <select id="hora" name="hora" disabled required>
            <option value="">--Seleccione primero fecha--</option>
        </select>

        <button id="btn_agendar" type="submit" disabled>Agendar cita</button>
    </form>
</div>
</body>
</html>
