<?php
include 'seguridad.php';
include 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];

function getMedico($pdo, $usuario_id) {
    $stmt = $pdo->prepare("
        SELECT m.id AS medico_id, u.nombre 
        FROM medicos m
        INNER JOIN usuarios u ON m.usuario_id = u.id
        WHERE m.usuario_id = ?
    ");
    $stmt->execute([$usuario_id]);
    return $stmt->fetch();
}

function formatearHora($hora) {
    return date("H:i", strtotime($hora));
}

function getCitasProximas($pdo, $medico_id) {
    $sql = "SELECT c.id, c.fecha, c.motivo, c.estado, p.nombre AS paciente
            FROM citas c
            INNER JOIN pacientes p ON c.paciente_id = p.id
            WHERE c.medico_id = ? AND c.estado = 'Programada' AND c.fecha >= NOW()
            ORDER BY c.fecha ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medico_id]);
    return $stmt->fetchAll();
}

function getHorarioSemanal($pdo, $medico_id) {
    $sql = "SELECT dia, hora_inicio, hora_fin FROM horarios 
            WHERE medico_id = ? 
            ORDER BY FIELD(dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'), hora_inicio";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medico_id]);
    return $stmt->fetchAll();
}

function getCitasHoy($pdo, $medico_id) {
    $sql = "SELECT c.fecha, p.nombre AS paciente
            FROM citas c
            JOIN pacientes p ON c.paciente_id = p.id
            WHERE c.medico_id = ? AND c.estado = 'Programada' AND DATE(c.fecha) = CURDATE()
            ORDER BY c.fecha ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medico_id]);
    return $stmt->fetchAll();
}

function contarCitasEnRango($pdo, $medico_id, $dias=7) {
    $sql = "SELECT COUNT(*) FROM citas WHERE medico_id = ? AND estado = 'Programada' AND fecha BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ? DAY)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$medico_id, $dias]);
    return $stmt->fetchColumn();
}

$medico = getMedico($pdo, $usuario_id);
if (!$medico) {
    die("⚠ Este usuario no está registrado como médico.");
}

$medico_id = $medico['medico_id'];
$nombre_medico = $medico['nombre'];
$citas = getCitasProximas($pdo, $medico_id);
$horarios = getHorarioSemanal($pdo, $medico_id);
$citasHoy = getCitasHoy($pdo, $medico_id);
$citasProx7Dias = contarCitasEnRango($pdo, $medico_id, 7);

$alertCitas = [];
foreach ($citasHoy as $c) {
    $fechaUnix = strtotime($c['fecha']);
    $diffMin = ($fechaUnix - time()) / 60;
    $alertCitas[] = [
        'paciente' => $c['paciente'],
        'hora' => date('H:i', $fechaUnix),
        'enMenosDeUnaHora' => $diffMin > 0 && $diffMin <= 60
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Panel Médico Avanzado</title>
<link rel="stylesheet" href="estilos/style.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body {
        background: #eef2f7;
        font-family: Arial, sans-serif;
        margin: 0; padding: 0;
        transition: background 0.3s, color 0.3s;
    }
    body.dark-mode {
        background: #121212;
        color: #ddd;
    }
    .container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        transition: background 0.3s, color 0.3s;
    }
    body.dark-mode .container {
        background: #1e1e1e;
    }
    h1, h2 {
        color: #2c3e50;
    }
    body.dark-mode h1, body.dark-mode h2 {
        color: #f0f0f0;
    }
    .welcome {
        margin-bottom: 30px;
        font-size: 1.5em;
        font-weight: 700;
    }
    .flex {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }
    .panel {
        flex: 1 1 480px;
        background: #fafafa;
        padding: 25px;
        border-radius: 12px;
        box-shadow: inset 0 0 8px rgba(0,0,0,0.05);
        transition: background 0.3s;
    }
    body.dark-mode .panel {
        background: #292929;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    th, td {
        padding: 14px 15px;
        border-bottom: 1px solid #ddd;
        text-align: left;
        font-size: 1rem;
    }
    body.dark-mode th, body.dark-mode td {
        border-color: #444;
    }
    th {
        background: #3498db;
        color: white;
        font-weight: 700;
        letter-spacing: 0.05em;
    }
    tbody tr:hover {
        background: #f1f9ff;
        transition: background 0.3s;
    }
    body.dark-mode tbody tr:hover {
        background: #3a3a3a;
    }
    .estado {
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 16px;
        color: white;
        text-align: center;
        width: 110px;
        display: inline-block;
        font-size: 0.95rem;
        user-select: none;
    }
    .programada { background-color: #3498db; }
    .atendida { background-color: #27ae60; }
    .cancelada { background-color: #e74c3c; }
    .btn-accion {
        margin-right: 8px;
        padding: 6px 14px;
        font-size: 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        user-select: none;
        transition: background-color 0.3s;
    }
    .btn-atendida {
        background-color: #27ae60;
        color: white;
    }
    .btn-cancelada {
        background-color: #e74c3c;
        color: white;
    }
    .btn-accion:hover {
        opacity: 0.85;
    }
    .no-data {
        font-style: italic;
        color: #777;
        margin-top: 20px;
        font-size: 1.1rem;
    }
    a.logout {
        position: fixed;
        top: 15px;
        right: 15px;
        background: #3498db;
        color: white;
        padding: 10px 18px;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.3s;
        z-index: 999;
    }
    a.logout:hover {
        background: #2980b9;
    }
    .dark-mode-toggle {
        position: fixed;
        top: 15px;
        left: 15px;
        background: #666;
        color: white;
        padding: 10px 18px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        user-select: none;
        transition: background 0.3s;
        z-index: 999;
    }
    .dark-mode-toggle:hover {
        background: #444;
    }
    .summary {
        margin-bottom: 30px;
        font-size: 1.2rem;
        font-weight: 600;
        color: #555;
    }
    body.dark-mode .summary {
        color: #bbb;
    }
</style>
</head>
<body>

<a href="logout.php" class="logout" title="Cerrar sesión">Cerrar sesión</a>
<div class="dark-mode-toggle" id="toggleDarkMode" title="Alternar modo oscuro">🌙</div>

<div class="container">
    <h1>Panel Médico Avanzado</h1>
    <p class="welcome">Bienvenido, Dr. <?= htmlspecialchars($nombre_medico) ?></p>
    
    <div class="summary">
        Tienes <strong><?= count($citasHoy) ?></strong> cita(s) para hoy y <strong><?= $citasProx7Dias ?></strong> en los próximos 7 días.
    </div>

    <div class="flex">
        <section class="panel">
            <h2>Próximas Citas</h2>
            <?php if (count($citas) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($citas as $cita): ?>
                    <tr>
                        <td><?= htmlspecialchars($cita['paciente']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($cita['fecha'])) ?></td>
                        <td><?= htmlspecialchars($cita['motivo']) ?></td>
                        <td>
                            <span class="estado <?= strtolower($cita['estado']) ?>">
                                <?= htmlspecialchars($cita['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($cita['estado'] === 'Programada'): ?>
                                <form action="modulos/citas/actualizar_estado.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $cita['id'] ?>">
                                    <input type="hidden" name="accion" value="Atendida">
                                    <button class="btn-accion btn-atendida" type="submit" title="Marcar como Atendida">✔</button>
                                </form>
                                <form action="modulos/citas/actualizar_estado.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $cita['id'] ?>">
                                    <input type="hidden" name="accion" value="Cancelada">
                                    <button class="btn-accion btn-cancelada" type="submit" title="Cancelar cita">✖</button>
                                </form>
                            <?php else: ?>
                                <em>-</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="no-data">No tienes citas programadas próximas.</p>
            <?php endif; ?>
        </section>

        <section class="panel">
            <h2>Horario Semanal</h2>
            <?php if (count($horarios) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($horarios as $h): ?>
                    <tr>
                        <td><?= htmlspecialchars($h['dia']) ?></td>
                        <td><?= formatearHora($h['hora_inicio']) ?></td>
                        <td><?= formatearHora($h['hora_fin']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="no-data">No tienes horarios asignados.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
    // Toggle modo oscuro
    const toggle = document.getElementById('toggleDarkMode');
    toggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        if(document.body.classList.contains('dark-mode')) {
            toggle.textContent = '☀️';
            localStorage.setItem('modoOscuro', 'true');
        } else {
            toggle.textContent = '🌙';
            localStorage.setItem('modoOscuro', 'false');
        }
    });
    // Aplicar modo oscuro guardado
    if(localStorage.getItem('modoOscuro') === 'true') {
        document.body.classList.add('dark-mode');
        toggle.textContent = '☀️';
    }

    // Mostrar alertas de citas de hoy con SweetAlert2
    const alertCitas = <?= json_encode($alertCitas); ?>;

    if(alertCitas.length > 0) {
        let htmlList = '<ul style="text-align:left; padding-left:20px;">';
        alertCitas.forEach(cita => {
            htmlList += `<li style="margin-bottom:6px;">
                <strong>${cita.hora}</strong> - ${cita.paciente}
                ${cita.enMenosDeUnaHora ? '<span style="color:#d33; font-weight:bold;">(En menos de 1 hora)</span>' : ''}
            </li>`;
        });
        htmlList += '</ul>';

        Swal.fire({
            icon: 'info',
            title: 'Recordatorios de hoy',
            html: `Tienes ${alertCitas.length} cita(s) programada(s) para hoy: ${htmlList}`,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#3085d6',
            backdrop: true,
            timer: 15000,
            timerProgressBar: true,
        });
    }
</script>

</body>
</html>
