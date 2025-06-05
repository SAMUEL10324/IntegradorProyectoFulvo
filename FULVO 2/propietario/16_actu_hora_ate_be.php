<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';

// Validación de campos obligatorios
if (
    empty($_POST['id_horarios_atencion']) ||
    empty($_POST['nuevo_dia']) ||
    empty($_POST['nuevo_apertura']) ||
    empty($_POST['nuevo_cierre']) ||
    empty($_POST['predio_id'])
) {
    echo "<script>alert('Faltan datos del formulario'); window.history.back();</script>";
    exit();
}

// Recolección de datos
$id_horario = $_POST['id_horarios_atencion'];
$nuevo_dia = $_POST['nuevo_dia'];
$nueva_apertura = $_POST['nuevo_apertura'];
$nueva_cierre = $_POST['nuevo_cierre'];
$predio_id = $_POST['predio_id'];

// Verificar si ya existe un horario para ese predio y ese día (distinto al que estamos modificando)
$existe_query = "
    SELECT id_horarios_atencion 
    FROM Horario_Atencion 
    WHERE Predio_id_predio = $predio_id 
    AND Dias_id_dias = $nuevo_dia 
    AND id_horarios_atencion != $id_horario
";
$existe_result = mysqli_query($conexion, $existe_query);

if (mysqli_num_rows($existe_result) > 0) {
    echo "<script>alert('Ya existe un horario para ese día en este predio. Modificá ese horario o elegí otro día.'); window.history.back();</script>";
    exit();
}

// Realizar la actualización
$update = "
    UPDATE Horario_Atencion 
    SET horario_apertura = '$nueva_apertura', 
        horario_cierre = '$nueva_cierre', 
        Dias_id_dias = '$nuevo_dia'
    WHERE id_horarios_atencion = $id_horario
";

if (mysqli_query($conexion, $update)) {
    echo "<script>alert('Horario actualizado correctamente'); window.location = '14_gestion_horarios.php';</script>";
} else {
    echo "<script>alert('Error al actualizar el horario'); window.history.back();</script>";
}

mysqli_close($conexion);
?>

