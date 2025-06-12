<?php
session_start();
include '../login/conexion_be.php';

// Validar sesión
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'cliente') {
    echo '<script>alert("Debes iniciar sesión."); window.location = "../login_registro.php";</script>';
    session_destroy();
    die();
}

// Obtener datos del formulario
$predio_id = $_POST['predio_id'];
$calificacion = intval($_POST['calificacion']);
$comentario = $_POST['comentario'];
$fecha = date('Y-m-d'); // Fecha actual

// Obtener ID del cliente desde sesión directamente
$correo = $_SESSION['correo'];
$consulta_cliente = "SELECT c.id_cliente
                     FROM Cliente c
                     JOIN Usuario u ON c.Usuario_id_usuario = u.id_usuario
                     WHERE u.correo_electronico = '$correo'"; 

$resultado_cliente = mysqli_query($conexion, $consulta_cliente);
$fila_cliente = mysqli_fetch_assoc($resultado_cliente);
$id_cliente = $fila_cliente['id_cliente'] ?? null;


if (!$id_cliente) {
    echo '<script>alert("No se pudo obtener el ID del cliente."); window.location = "3_alta_reseña.php";</script>';
    exit();
}

// Validar calificación
if ($calificacion < 1 || $calificacion > 10) {
    echo '<script>alert("La calificación debe estar entre 1 y 10."); window.location = "3_alta_reseña.php";</script>';
    exit();
}

// Insertar reseña
$insertar = "INSERT INTO Reseña (fecha_reseña, calificacion, comentario, Cliente_id_cliente, Predio_id_predio)
             VALUES ('$fecha', '$calificacion', '$comentario', '$id_cliente', '$predio_id')";

$resultado = mysqli_query($conexion, $insertar);

if ($resultado) {
    echo '<script>alert("¡Reseña guardada correctamente!"); window.location = "6_gestion_reseña.php";</script>';
} else {
    echo '<script>alert("Error al guardar la reseña: ' . mysqli_error($conexion) . '"); window.location = "3_alta_reseña.php";</script>';
}
?>