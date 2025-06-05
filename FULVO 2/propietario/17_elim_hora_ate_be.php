<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';

if (empty($_POST['id_horarios_atencion'])) {
    echo "<script>alert('Debe seleccionar un horario a eliminar.'); window.history.back();</script>";
    exit();
}

$id_horario = $_POST['id_horarios_atencion'];

// Eliminar el horario
$delete = "DELETE FROM Horario_Atencion WHERE id_horarios_atencion = $id_horario";

if (mysqli_query($conexion, $delete)) {
    echo "<script>alert('Horario eliminado exitosamente.'); window.location = '17_elim_hora_ate.php';</script>";
} else {
    echo "<script>alert('Error al eliminar el horario.'); window.history.back();</script>";
}

mysqli_close($conexion);
?>
