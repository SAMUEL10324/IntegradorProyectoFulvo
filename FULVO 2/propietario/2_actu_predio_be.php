<?php
session_start();
if(!isset($_SESSION['correo']) || $_SESSION['rol'] != 'propietario'){
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';

$id_predio = $_POST['id_predio'];
$id_ubicacion = $_POST['id_ubicacion'];

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$contacto = $_POST['contacto'];

$calle = $_POST['calle'];
$numero = $_POST['numero'];
$ciudad = $_POST['ciudad'];
$provincia = $_POST['provincia'];

// 1. Actualizar imagen si hay nueva
if ($_FILES['foto_predio_nueva']['name']) {
    $foto_predio = $_FILES['foto_predio_nueva']['name'];
    $ruta_temporal = $_FILES['foto_predio_nueva']['tmp_name'];
    $carpeta_destino = 'imagenes/';
    $nombre_imagen = uniqid() . '_' . $foto_predio;
    $ruta_final = $carpeta_destino . $nombre_imagen;

    if (!move_uploaded_file($ruta_temporal, $ruta_final)) {
        echo '<script>alert("Error al subir imagen nueva."); window.location = "2_actu_predio.php?id=' . $id_predio . '";</script>';
        exit();
    }

    $update_predio = "UPDATE Predio SET 
        nombre='$nombre', descripcion='$descripcion', contacto='$contacto', foto_predio='$ruta_final'
        WHERE id_predio=$id_predio";
} else {
    $update_predio = "UPDATE Predio SET 
        nombre='$nombre', descripcion='$descripcion', contacto='$contacto'
        WHERE id_predio=$id_predio";
}

// 2. Ejecutar actualizaciones
$update_ubicacion = "UPDATE Ubicacion SET 
    calle='$calle', numero='$numero', ciudad='$ciudad', provincia='$provincia'
    WHERE id_ubicacion=$id_ubicacion";

$ok1 = mysqli_query($conexion, $update_predio);
$ok2 = mysqli_query($conexion, $update_ubicacion);

if ($ok1 && $ok2) {
    echo '<script>alert("Predio actualizado correctamente."); window.location = "../propietario_main.php";</script>';
} else {
    echo '<script>alert("Error al actualizar."); window.location = "2_actu_predio.php?id=' . $id_predio . '";</script>';
}
?>
