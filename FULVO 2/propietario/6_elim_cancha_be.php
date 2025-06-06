<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';

$id_cancha = $_POST['id_cancha'] ?? null;

if (!$id_cancha) {
    echo "<script>alert('Falta el ID de la cancha.'); window.history.back();</script>";
    exit();
}

// Verificar si la cancha tiene reservas asociadas
$verificar_reservas = mysqli_query($conexion, "
    SELECT COUNT(*) AS total 
    FROM Detalle_Reserva 
    WHERE Cancha_id_cancha = '$id_cancha'
");

$datos = mysqli_fetch_assoc($verificar_reservas);

if ($datos['total'] > 0) {
    echo "<script>alert('No se puede eliminar la cancha porque tiene reservas asociadas.'); window.history.back();</script>";
    exit();
}

// Si no hay reservas asociadas, se elimina
$eliminar = mysqli_query($conexion, "
    DELETE FROM Cancha WHERE id_cancha = '$id_cancha'
");

if ($eliminar) {
    echo "<script>alert('Cancha eliminada correctamente.'); window.location = '12_gestion_canchas.php';</script>";
} else {
    echo "<script>alert('Error al eliminar la cancha.'); window.history.back();</script>";
}

mysqli_close($conexion);
?>
