<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';

$id_usuario = $_SESSION['id_usuario'];

// Obtener nombre del propietario
$res_usuario = mysqli_query($conexion, "SELECT nombre, apellido FROM Usuario WHERE id_usuario = $id_usuario");
$usuario = mysqli_fetch_assoc($res_usuario);

// Obtener ID del propietario
$res_prop = mysqli_query($conexion, "SELECT id_propietario FROM Propietario WHERE Usuario_id_usuario = $id_usuario");
$prop = mysqli_fetch_assoc($res_prop);
$id_propietario = $prop['id_propietario'] ?? null;

// Obtener todos los predios del propietario con horarios
$predios = mysqli_query($conexion, "
    SELECT p.id_predio, p.nombre AS nombre_predio, d.nombre AS dia, h.horario_apertura, h.horario_cierre
    FROM Predio p
    LEFT JOIN Horario_Atencion h ON p.id_predio = h.Predio_id_predio
    LEFT JOIN Dias d ON h.Dias_id_dias = d.id_dias
    WHERE p.Propietario_id_propietario = $id_propietario
    ORDER BY p.id_predio, h.Dias_id_dias
");

// Agrupar datos por predio
$datos_predios = [];
while ($fila = mysqli_fetch_assoc($predios)) {
    $id = $fila['id_predio'];
    if (!isset($datos_predios[$id])) {
        $datos_predios[$id] = [
            'nombre_predio' => $fila['nombre_predio'],
            'horarios' => []
        ];
    }
    if ($fila['dia']) {
        $datos_predios[$id]['horarios'][] = [
            'dia' => $fila['dia'],
            'apertura' => $fila['horario_apertura'],
            'cierre' => $fila['horario_cierre']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Horarios</title>
    <link rel="stylesheet" href="estilosPropietario.css">
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <div class="acciones-contenedor">
            <span>¡Gestioná los horarios de atención de tus predios!</span>
            <div class="acciones">
                <a href="15_alta_hora_ate.php">Registrar Horario</a>
                <a href="16_actu_hora_ate.php">Cambiar Horarios</a>
                <a href="17_elim_hora_ate.php">Eliminar Horario</a>
            </div>
        </div>
        <a href="../propietario_main.php" class="btn_volver">Volver</a>
    </header>

    <main>
        <?php foreach ($datos_predios as $predio): ?>
            <div class="predio-card">
                <h2><?= htmlspecialchars($predio['nombre_predio']) ?></h2>
                <?php if (count($predio['horarios']) > 0): ?>
                    <ul>
                        <?php foreach ($predio['horarios'] as $horario): ?>
                            <li><strong><?= $horario['dia'] ?>:</strong> <?= $horario['apertura'] ?> a <?= $horario['cierre'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No hay horarios cargados.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>
