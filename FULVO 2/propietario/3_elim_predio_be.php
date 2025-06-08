<?php
session_start();
include '../login/conexion_be.php';

if (!isset($_SESSION['correo']) || $_SESSION['rol'] != 'propietario') {
    echo '
        <script>
            alert("Acceso denegado. Inicia sesión.");
            window.location = "../login_registro.php";
        </script>
    ';
    session_destroy();
    die();
}

$id_predio = $_POST['id_predio'] ?? null;

if (!$id_predio) {
    echo '
        <script>
            alert("Debe seleccionar un predio.");
            window.location = "3_elim_predio.php";
        </script>
    ';
    exit;
}

// Obtener el ID de ubicación del predio antes de eliminarlo
$ubicacion_result = mysqli_query($conexion, "SELECT Ubicacion_id_ubicacion FROM predio WHERE id_predio = '$id_predio'");
$ubicacion_data = mysqli_fetch_assoc($ubicacion_result);
$id_ubicacion = $ubicacion_data['Ubicacion_id_ubicacion'] ?? null;

// Verificar si alguna cancha asociada tiene reservas
$consulta_reservas = "
    SELECT dr.id_detalle_reserva
    FROM cancha c
    JOIN Detalle_Reserva dr ON dr.Cancha_id_cancha = c.id_cancha
    WHERE c.Predio_id_predio = '$id_predio'
";

$resultado_reservas = mysqli_query($conexion, $consulta_reservas);

if (mysqli_num_rows($resultado_reservas) > 0) {
    echo '
        <script>
            alert("No se puede eliminar el predio porque tiene reservas asociadas.");
            window.location = "3_elim_predio.php";
        </script>
    ';
    exit;
}

// Eliminar canchas del predio
mysqli_query($conexion, "DELETE FROM cancha WHERE Predio_id_predio = '$id_predio'");

// Eliminar predio
$eliminado = mysqli_query($conexion, "DELETE FROM predio WHERE id_predio = '$id_predio'");

// Si se eliminó el predio, eliminar la ubicación asociada
if ($eliminado && $id_ubicacion) {
    mysqli_query($conexion, "DELETE FROM ubicacion WHERE id_ubicacion = '$id_ubicacion'");
}

if ($eliminado) {
    echo '
        <script>
            alert("Predio eliminado correctamente.");
            window.location = "11_gestion_predio.php";
        </script>
    ';
} else {
    echo '
        <script>
            alert("Error al eliminar el predio.");
            window.location = "3_elim_predio.php";
        </script>
    ';
}

mysqli_close($conexion);
