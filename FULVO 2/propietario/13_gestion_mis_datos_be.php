<?php
session_start();
include '../login/conexion_be.php';

if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: ../login_registro.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Datos del formulario
$nombre = trim($_POST['nombre']);
$apellido = trim($_POST['apellido']);
$correo = trim($_POST['correo']);
$dni = trim($_POST['dni']);
$cuil = trim($_POST['cuil']);
$contrasena = trim($_POST['contrasena']);

// Verifica el correo duplicado (excepto el propio)
$ver_correo = mysqli_query($conexion, "SELECT id_usuario FROM Usuario WHERE correo_electronico = '$correo' AND id_usuario != $id_usuario");
if (mysqli_num_rows($ver_correo) > 0) {
    echo "<script>alert('El correo ya está en uso por otro usuario.'); window.location = '13_gestion_mis_datos.php';</script>";
    exit();
}

// Verifica DNI duplicado (excepto el propio)
$ver_dni = mysqli_query($conexion, "SELECT id_usuario FROM Usuario WHERE dni = '$dni' AND id_usuario != $id_usuario");
if (mysqli_num_rows($ver_dni) > 0) {
    echo "<script>alert('El DNI ya está en uso por otro usuario.'); window.location = '13_gestion_mis_datos.php';</script>";
    exit();
}

// Verifica el CUIL duplicado (excepto el propio)
$ver_cuil = mysqli_query($conexion, "
    SELECT id_propietario FROM Propietario 
    WHERE cuil = '$cuil' AND Usuario_id_usuario != $id_usuario
");
if (mysqli_num_rows($ver_cuil) > 0) {
    echo "<script>alert('El CUIL ya está en uso por otro propietario.'); window.location = '13_gestion_mis_datos.php';</script>";
    exit();
}

// Actualizar usuario
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
mysqli_query($conexion, $update_usuario);

// Actualizar propietario
$update_propietario = "
    UPDATE Propietario 
    SET cuil = '$cuil'
    WHERE Usuario_id_usuario = $id_usuario
";
mysqli_query($conexion, $update_propietario);

echo "<script>alert('Datos actualizados exitosamente.'); window.location = '../propietario_main.php';</script>";
exit();
?>
