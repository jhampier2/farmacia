<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a la Clínica Nova</title>
    <link rel="stylesheet" href="estilos/style.css">
    <style>
        .inicio-container {
            width: 400px;
            margin: 80px auto;
            text-align: center;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .inicio-container h1 {
            margin-bottom: 25px;
        }
        .inicio-container a {
            display: block;
            padding: 12px;
            margin: 10px 0;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .inicio-container a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="inicio-container">
        <h1>Sistema de Clínica</h1>
        <a href="registro_paciente.php">Registrarse como Paciente</a>
        <a href="login.php">Iniciar Sesión como paciente</a>
        <a href="index.php">Iniciar Sesión (Médico / Administrador)</a>
        <a href="registro_usuario.php">Registrarse como((Médico / Administrador))</a>
    </div>
</body>
</html>
