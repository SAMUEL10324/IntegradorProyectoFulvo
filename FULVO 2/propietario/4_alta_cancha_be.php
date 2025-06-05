<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario') {
    header("Location:../login_registro.php");
    exit();
}

include '../login/conexion_be.php';

$num_cancha = $_POST['num_cancha'];
$precio_hora = $_POST['precio_hora'];
$capacidad = $_POST['capacidad'];
$tipo_cancha = $_POST['tipo_cancha'];
$disponibilidad = $_POST['disponibilidad'];
$predio_id = $_POST['predio_id'];

// Validar predio
if (empty($predio_id)) {
    echo "<script>alert('Debe seleccionar un predio.'); window.history.back();</script>";
    exit();
}

// Manejo de imagen
$nombre_imagen = null;
if (!empty($_FILES['imagen_cancha']['name'])) {
    $imagen_nombre = $_FILES['imagen_cancha']['name'];
    $ruta_temporal = $_FILES['imagen_cancha']['tmp_name'];
    $carpeta_destino = 'imagenes/';
    $nombre_imagen = uniqid() . '_' . $imagen_nombre;
    $ruta_final = $carpeta_destino . $nombre_imagen;

    if (!move_uploaded_file($ruta_temporal, $ruta_final)) {
        echo "<script>alert('Error al subir la imagen.'); window.history.back();</script>";
        exit();
    }
} else {
    $ruta_final = null;
}

// Insertar cancha
$query = "INSERT INTO Cancha (
    num_cancha, precio_hora, capacidad, tipo_cancha, disponibilidad, imagen_cancha, Predio_id_predio
) VALUES (
    '$num_cancha', '$precio_hora', '$capacidad', '$tipo_cancha', '$disponibilidad', 
    " . ($ruta_final ? "'$ruta_final'" : "NULL") . ", '$predio_id'
)";

if (mysqli_query($conexion, $query)) {
    echo "<script>alert('Cancha registrada exitosamente.'); window.location = '4_alta_cancha_be';</script>";
} else {
    echo "<script>alert('Error al registrar la cancha.'); window.history.back();</script>";
}

mysqli_close($conexion);
?>