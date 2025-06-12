<?php
session_start();
include '../login/conexion_be.php';

// Verificación de sesión
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'cliente') {
    echo '<script>alert("Debes iniciar sesión."); window.location = "../login_registro.php";</script>';
    session_destroy();
    exit();
}

// Verificar que venga el ID de la reseña
$id_reseña = $_POST['id_reseña'] ?? null;
if (!$id_reseña) {
    echo '<script>alert("ID de reseña no válido."); window.location = "6_gestion_reseña.php";</script>';
    exit();
}

// Obtener el id_cliente según el correo
$correo = $_SESSION['correo'];
$consulta_cliente = "SELECT c.id_cliente
                     FROM Cliente c
                     JOIN Usuario u ON c.Usuario_id_usuario = u.id_usuario
                     WHERE u.correo_electronico = '$correo'";
$resultado_cliente = mysqli_query($conexion, $consulta_cliente);
$fila_cliente = mysqli_fetch_assoc($resultado_cliente);
$id_cliente = $fila_cliente['id_cliente'] ?? null;

if (!$id_cliente) {
    echo '<script>alert("No se pudo verificar tu identidad."); window.location = "6_gestion_reseña.php";</script>';
    exit();
}

// Eliminar solo si la reseña pertenece al cliente
$eliminar = "DELETE FROM Reseña WHERE id_reseña = $id_reseña AND Cliente_id_cliente = $id_cliente";
$resultado = mysqli_query($conexion, $eliminar);

if ($resultado && mysqli_affected_rows($conexion) > 0) {
    echo '<script>alert("Reseña eliminada correctamente."); window.location = "6_gestion_reseña.php";</script>';
} else {
    echo '<script>alert("No se pudo eliminar la reseña. Puede que no te pertenezca."); window.location = "6_gestion_reseña.php";</script>';
}
?>
