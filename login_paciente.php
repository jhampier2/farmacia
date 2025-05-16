<?php
session_start();
include 'conexion.php';

$error = '';
$nombre = '';
$dni = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $dni = trim($_POST['dni']);

    if ($nombre === '' || $dni === '') {
        $error = 'Por favor, ingresa nombre y DNI.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE nombre = ? AND dni = ?");
        $stmt->execute([$nombre, $dni]);
        $paciente = $stmt->fetch();

        if ($paciente) {
            $_SESSION['paciente_id'] = $paciente['id'];
            $_SESSION['paciente_nombre'] = $paciente['nombre'];
            header("Location: panel_paciente.php");
            exit;
        } else {
            $error = 'Nombre o DNI incorrectos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Ingreso Paciente</title>
<style>
/* Estilos simplificados */
body { font-family: Arial,sans-serif; background:#eef2f7; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;}
.login-box { background:#fff; padding:30px 35px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); width:360px; }
h2 { text-align:center; margin-bottom:25px; color:#333; }
input { width:100%; padding:12px 10px; margin-bottom:18px; border:1px solid #bbb; border-radius:6px; font-size:16px; box-sizing:border-box; transition:border-color .3s ease; }
input:focus { border-color:#007bff; outline:none; }
button { width:100%; padding:14px; background:#007bff; color:#fff; font-weight:bold; font-size:17px; border:none; border-radius:7px; cursor:pointer; transition:background-color .3s ease; }
button:hover { background:#0056b3; }
.error { color:#b00020; background:#fdd; padding:10px; margin-bottom:18px; border-radius:6px; text-align:center; font-weight:bold; box-shadow:0 0 5px rgba(176,0,32,0.3); }
a { display:block; text-align:center; margin-top:20px; color:#007bff; text-decoration:none; font-weight:600; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="login-box">
<h2>Ingreso Paciente</h2>

<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <input type="text" name="nombre" placeholder="Nombre completo" value="<?= htmlspecialchars($nombre) ?>" required autocomplete="name" autofocus>
    <input type="text" name="dni" placeholder="DNI" value="<?= htmlspecialchars($dni) ?>" required autocomplete="off" maxlength="15" pattern="[0-9\-]+" title="Solo números y guiones permitidos">
    <button type="submit">Iniciar</button>
</form>

<a href="index.php">← Volver al inicio</a>
</div>

</body>
</html>
