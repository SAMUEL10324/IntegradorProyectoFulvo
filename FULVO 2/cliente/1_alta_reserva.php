<?php
session_start();
include '../login/conexion_be.php';

if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'cliente') {
    echo '<script>alert("Debes iniciar sesión."); window.location = "../login_registro.php";</script>';
    session_destroy();
    die();
}

$id_predio = $_POST['id_predio'] ?? null;
$fecha = $_POST['fecha'] ?? null;

if (!$id_predio || !$fecha) {
    echo '<script>alert("Faltan datos."); window.location = "../cliente_main.php";</script>';
    exit;
}

$query_canchas = mysqli_query($conexion, "
    SELECT id_cancha, num_cancha, tipo_cancha, precio_hora, imagen_cancha
    FROM Cancha
    WHERE Predio_id_predio = $id_predio
");
$canchas = mysqli_fetch_all($query_canchas, MYSQLI_ASSOC);

$dia_semana = date('l', strtotime($fecha));
$dias_traducidos = [
    'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miercoles',
    'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sabado', 'Sunday' => 'Domingo'
];
$dia_es = $dias_traducidos[$dia_semana] ?? '';

$query_horarios = mysqli_query($conexion, "
    SELECT horario_apertura, horario_cierre
    FROM Horario_Atencion h
    JOIN Dias d ON h.Dias_id_dias = d.id_dias
    WHERE h.Predio_id_predio = $id_predio AND d.nombre = '$dia_es'
");
$horarios = mysqli_fetch_assoc($query_horarios);

function generarHorarios($inicio, $fin, $reservas_existentes) {
    $horarios = [];
    $current = strtotime($inicio);
    $end = strtotime($fin);

    if ($end <= $current) {
        $end = strtotime($fin . ' +1 day');
    }

    while ($current + 3600 <= $end) {
        $inicio_slot = date("H:i:s", $current);
        $fin_slot = date("H:i:s", $current + 3600);

        // Verificar si este rango se solapa con alguno ya reservado
        $solapado = false;
        foreach ($reservas_existentes as $reserva) {
            $reserva_inicio = strtotime($reserva['hora_inicio']);
            $reserva_fin = strtotime($reserva['hora_salida']);

            $slot_inicio = strtotime($inicio_slot);
            $slot_fin = strtotime($fin_slot);

            // Si se solapan (inicio < fin_reserva && fin > inicio_reserva)
            if ($slot_inicio < $reserva_fin && $slot_fin > $reserva_inicio) {
                $solapado = true;
                break;
            }
        }

        if (!$solapado) {
            $horarios[] = "$inicio_slot - $fin_slot";
        }

        $current += 3600;
    }

    return $horarios;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar Cancha</title>
    <link rel="stylesheet" href="estilosclientes.css">
</head>
<body>
<header class="barra_superior">
    <h1>FULVO.COM</h1>
    <a href="../cliente_main.php" class="btn_volver">Volver</a>
</header>
<main>
    <h2>Seleccione la cancha a reservar</h2>

    <?php if (count($canchas) > 0): ?>
        <?php foreach ($canchas as $cancha): ?>
            <?php
            $id_cancha = $cancha['id_cancha'];
            $reservas_query = mysqli_query($conexion, "
                SELECT hora_inicio, hora_salida
                FROM Detalle_Reserva dr
                JOIN Reserva r ON dr.Reserva_id_reserva = r.id_reserva
                WHERE r.fecha = '$fecha' AND dr.Cancha_id_cancha = $id_cancha
            ");
            $horas_reservadas = mysqli_fetch_all($reservas_query, MYSQLI_ASSOC);

            $horarios_disponibles = [];
            if ($horarios && $horarios['horario_apertura'] && $horarios['horario_cierre']) {
                $horarios_disponibles = generarHorarios($horarios['horario_apertura'], $horarios['horario_cierre'], $horas_reservadas);
            }
            ?>
            <form action="1_alta_reserva_be.php" method="POST" class="cancha-card">
                <input type="hidden" name="id_predio" value="<?= $id_predio ?>">
                <input type="hidden" name="id_cancha" value="<?= $cancha['id_cancha'] ?>">
                <input type="hidden" name="fecha" value="<?= $fecha ?>">

                <img src="../propietario/<?= $cancha['imagen_cancha'] ?>" alt="Imagen Cancha">
                <div class="cancha-info">
                    <h3>Cancha Nº<?= $cancha['num_cancha'] ?></h3>
                    <p><strong>Tipo:</strong> <?= $cancha['tipo_cancha'] ?></p>
                    <p><strong>Precio por hora:</strong> $<?= number_format($cancha['precio_hora'], 2) ?></p>

                    <?php if (count($horarios_disponibles) > 0): ?>
                        <label for="horarios[]">Seleccionar horarios:</label><br>
                        <select name="horarios[]" multiple required>
                            <?php foreach ($horarios_disponibles as $h): ?>
                                <option value="<?= $h ?>"><?= $h ?></option>
                            <?php endforeach; ?>
                        </select>
                        <br><small>Mantener presionado CRTL para seleccionar más de un horario.</small><br>
                        <button type="submit">Reservar</button>
                    <?php else: ?>
                        <p><strong>Sin horarios disponibles para este día.</strong></p>
                    <?php endif; ?>
                </div>
            </form>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay canchas registradas para este predio.</p>
    <?php endif; ?>
</main>
</body>
</html>






