<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';

$id_cancha = $_POST['id_cancha'];
$num_cancha = $_POST['num_cancha'];
$precio_hora = $_POST['precio_hora'];
$capacidad = $_POST['capacidad'];
$tipo_cancha = $_POST['tipo_cancha'];
$disponibilidad = $_POST['disponibilidad'];

$imagen_path = null;
if (isset($_FILES['imagen_cancha']) && $_FILES['imagen_cancha']['error'] === UPLOAD_ERR_OK) {
    $nombre_archivo = basename($_FILES['imagen_cancha']['name']);
    $ruta_temporal = $_FILES['imagen_cancha']['tmp_name'];
    $ruta_destino = "imagenes/" . time() . "_" . $nombre_archivo;

    if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
        $imagen_path = $ruta_destino;
    } else {
        echo "<script>alert('Error al subir la imagen.'); window.history.back();</script>";
        exit();
    }

    $update_cancha = "UPDATE Cancha SET 
    num_cancha= '$num_cancha', precio_hora= '$precio_hora', capacidad= '$capacidad', tipo_cancha= '$tipo_cancha', disponibilidad= '$disponibilidad', imagen_cancha = '$imagen_path'
    WHERE id_cancha = '$id_cancha'";
}else {
    $update_cancha = "UPDATE Cancha SET 
    num_cancha= '$num_cancha', precio_hora= '$precio_hora', capacidad= '$capacidad', tipo_cancha= '$tipo_cancha', disponibilidad= '$disponibilidad
    WHERE id_cancha = '$id_cancha'";
}

if (mysqli_query($conexion, $update_cancha)) {
    echo "<script>alert('Cancha actualizada correctamente.'); window.location = '12_gestion_canchas.php';</script>";
} else {
    echo "<script>alert('Error al actualizar la cancha.'); window.history.back();</script>";
}

?>