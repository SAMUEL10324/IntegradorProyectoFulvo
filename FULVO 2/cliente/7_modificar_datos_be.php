<?php
session_start();
include '../login/conexion_be.php';

if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login_registro.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Datos del formulario
$nombre = trim($_POST['nombre']);
$apellido = trim($_POST['apellido']);
$correo = trim($_POST['correo']);
$dni = trim($_POST['dni']);
$contrasena = trim($_POST['contrasena']);

// Validar correo duplicado (excepto el propio)
$ver_correo = mysqli_query($conexion, "SELECT id_usuario FROM Usuario WHERE correo_electronico = '$correo' AND id_usuario != $id_usuario");
if (mysqli_num_rows($ver_correo) > 0) {
    echo "<script>alert('El correo ya está en uso por otro usuario.'); window.location = '7_gestionar_datos.php';</script>";
    exit();
}

// Validar DNI duplicado (excepto el propio)
$ver_dni = mysqli_query($conexion, "SELECT id_usuario FROM Usuario WHERE dni = '$dni' AND id_usuario != $id_usuario");
if (mysqli_num_rows($ver_dni) > 0) {
    echo "<script>alert('El DNI ya está en uso por otro usuario.'); window.location = '7_gestionar_datos.php';</script>";
    exit();
}

// Construir consulta de actualización
if (!empty($contrasena)) {
    $contrasena_hash = hash('sha512', $contrasena);
    $update_usuario = "
        UPDATE Usuario 
        SET nombre='$nombre', apellido='$apellido', correo_electronico='$correo', dni='$dni', contraseña='$contrasena_hash' 
        WHERE id_usuario = $id_usuario
    ";
} else {
    $update_usuario = "
        UPDATE Usuario 
        SET nombre='$nombre', apellido='$apellido', correo_electronico='$correo', dni='$dni'
        WHERE id_usuario = $id_usuario
    ";
}

// Ejecutar actualización
mysqli_query($conexion, $update_usuario);

echo "<script>alert('Datos actualizados exitosamente.'); window.location = '../cliente_main.php';</script>";
exit();
?>