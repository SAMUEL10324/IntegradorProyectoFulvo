<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';
$id_usuario = $_SESSION['id_usuario'];

// Obtener ID del propietario
$res_prop = mysqli_query($conexion, "SELECT id_propietario FROM Propietario WHERE Usuario_id_usuario = $id_usuario");
$prop = mysqli_fetch_assoc($res_prop);
$id_propietario = $prop['id_propietario'] ?? null;

// Consultas
date_default_timezone_set('America/Argentina/Buenos_Aires');
$hoy = date('Y-m-d');
$hora_actual = date('H:i:s');

// Reservas FUTURAS (incluye hoy pero con hora mayor)
$reservas_futuras = mysqli_query($conexion, "
    SELECT r.num_reserva, r.fecha, r.estado_reserva,
           d.hora_inicio, d.hora_salida,
           c.num_cancha, p.nombre AS nombre_predio,
           u.nombre AS nombre_cliente, u.apellido AS apellido_cliente
    FROM Reserva r
    JOIN Detalle_Reserva d ON r.id_reserva = d.Reserva_id_reserva
    JOIN Cancha c ON d.Cancha_id_cancha = c.id_cancha
    JOIN Predio p ON c.Predio_id_predio = p.id_predio
    JOIN Cliente cl ON r.Cliente_id_cliente = cl.id_cliente
    JOIN Usuario u ON cl.Usuario_id_usuario = u.id_usuario
    WHERE p.Propietario_id_propietario = $id_propietario
    AND (
        r.fecha > '$hoy' OR
        (r.fecha = '$hoy' AND d.hora_salida > '$hora_actual')
    )
    ORDER BY r.fecha ASC, d.hora_inicio ASC
");

// Reservas HISTÓRICAS (fecha pasada o hoy pero ya cumplidas)
$reservas_historicas = mysqli_query($conexion, "
    SELECT r.num_reserva, r.fecha, r.estado_reserva,
           d.hora_inicio, d.hora_salida,
           c.num_cancha, p.nombre AS nombre_predio,
           u.nombre AS nombre_cliente, u.apellido AS apellido_cliente
    FROM Reserva r
    JOIN Detalle_Reserva d ON r.id_reserva = d.Reserva_id_reserva
    JOIN Cancha c ON d.Cancha_id_cancha = c.id_cancha
    JOIN Predio p ON c.Predio_id_predio = p.id_predio
    JOIN Cliente cl ON r.Cliente_id_cliente = cl.id_cliente
    JOIN Usuario u ON cl.Usuario_id_usuario = u.id_usuario
    WHERE p.Propietario_id_propietario = $id_propietario
    AND (
        r.fecha < '$hoy' OR
        (r.fecha = '$hoy' AND d.hora_salida <= '$hora_actual')
    )
    ORDER BY r.fecha DESC, d.hora_inicio DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas</title>
    <link rel="stylesheet" href="estilosPropietario.css">
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <a href="../propietario_main.php" class="btn_volver">Volver</a>
    </header>
    <main class="reservas-contenedor">
        <section class="reservas-panel-act">
            <h2>📅 Reservas Actuales</h2>
            <?php while ($res = mysqli_fetch_assoc($reservas_futuras)): ?>
                <div class="reserva-item">
                    <p><strong>Reserva Nº:</strong> <?= $res['num_reserva'] ?></p>
                    <p><strong>Predio:</strong> <?= $res['nombre_predio'] ?></p>
                    <p><strong>Cancha:</strong> <?= $res['num_cancha'] ?> ⚽</p>
                    <p><strong>Cliente:</strong> <?= $res['nombre_cliente'] . ' ' . $res['apellido_cliente'] ?></p>
                    <p class="reserva-fecha"><strong>📅 Fecha:</strong> <?= $res['fecha'] ?></p>
                    <p class="reserva-hora"><strong>⏰ Hora:</strong> <?= $res['hora_inicio'] ?> a <?= $res['hora_salida'] ?></p>
                    <p><strong>Estado:</strong> <?= ucfirst($res['estado_reserva']) ?></p>
                    <hr>
                </div>
            <?php endwhile; ?>
        </section>

        <section class="reservas-panel-hist">
            <h2>📜 Historial de Reservas</h2>
            <?php while ($res = mysqli_fetch_assoc($reservas_historicas)): ?>
                <div class="reserva-item">
                    <p><strong>Reserva Nº:</strong> <?= $res['num_reserva'] ?></p>
                    <p><strong>Predio:</strong> <?= $res['nombre_predio'] ?></p>
                    <p><strong>Cancha:</strong> <?= $res['num_cancha'] ?> ⚽</p>
                    <p><strong>Cliente:</strong> <?= $res['nombre_cliente'] . ' ' . $res['apellido_cliente'] ?></p>
                    <p class="reserva-fecha"><strong>📅 Fecha:</strong> <?= $res['fecha'] ?></p>
                    <p class="reserva-hora"><strong>⏰ Hora:</strong> <?= $res['hora_inicio'] ?> a <?= $res['hora_salida'] ?></p>
                    <p><strong>Estado:</strong> <?= ucfirst($res['estado_reserva']) ?></p>
                    <hr>
                </div>
            <?php endwhile; ?>
        </section>
    </main>
</body>
</html>
