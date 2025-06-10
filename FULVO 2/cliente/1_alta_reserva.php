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

// Obtener canchas del predio
$query_canchas = mysqli_query($conexion, "
    SELECT id_cancha, num_cancha, tipo_cancha, precio_hora, imagen_cancha
    FROM Cancha
    WHERE Predio_id_predio = $id_predio
");
$canchas = mysqli_fetch_all($query_canchas, MYSQLI_ASSOC);

// Traducir día
$dia_semana = date('l', strtotime($fecha));
$dias_traducidos = [
    'Monday' => 'Lunes',
    'Tuesday' => 'Martes',
    'Wednesday' => 'Miercoles',
    'Thursday' => 'Jueves',
    'Friday' => 'Viernes',
    'Saturday' => 'Sabado',
    'Sunday' => 'Domingo'
];
$dia_es = $dias_traducidos[$dia_semana] ?? '';

// Obtener horario de atención para ese día
$query_horarios = mysqli_query($conexion, "
    SELECT horario_apertura, horario_cierre
    FROM Horario_Atencion h
    JOIN Dias d ON h.Dias_id_dias = d.id_dias
    WHERE h.Predio_id_predio = $id_predio AND d.nombre = '$dia_es'
");

$horarios = mysqli_fetch_assoc($query_horarios);

function generarHorarios($inicio, $fin) {
    $horarios = [];
    $current = strtotime($inicio);
    $end = strtotime($fin);
    while ($current < $end) {
        $inicio_slot = date("H:i", $current);
        $fin_slot = date("H:i", $current + 3600);
        if (strtotime($fin_slot) <= $end) {
            $horarios[] = "$inicio_slot - $fin_slot";
        }
        $current += 3600;
    }
    return $horarios;
}

$horarios_disponibles = [];

if ($horarios && $horarios['horario_apertura'] && $horarios['horario_cierre']) {
    $horarios_disponibles = generarHorarios($horarios['horario_apertura'], $horarios['horario_cierre']);
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
    <h2>Seleccioná una cancha para reservar</h2>

    <?php if (count($canchas) > 0): ?>
        <?php foreach ($canchas as $cancha): ?>
            <form action="2_guardar_reserva.php" method="POST" class="cancha-card">
                <input type="hidden" name="id_predio" value="<?= $id_predio ?>">
                <input type="hidden" name="id_cancha" value="<?= $cancha['id_cancha'] ?>">
                <input type="hidden" name="fecha" value="<?= $fecha ?>">

                <img src="../propietario/<?= $cancha['imagen_cancha'] ?>" alt="Imagen Cancha">

                <div class="cancha-info">
                    <h3>Cancha Nº<?= $cancha['num_cancha'] ?></h3>
                    <p><strong>Tipo:</strong> <?= $cancha['tipo_cancha'] ?></p>
                    <p><strong>Precio por hora:</strong> $<?= number_format($cancha['precio_hora'], 2) ?></p>

                    <?php if (count($horarios_disponibles) > 0): ?>
                        <label for="horario">Seleccionar horario:</label><br>
                        <select name="horario" required>
                            <option value="">-- Elegir horario --</option>
                            <?php foreach ($horarios_disponibles as $h): ?>
                                <option value="<?= $h ?>"><?= $h ?></option>
                            <?php endforeach; ?>
                        </select><br>
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



