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

$consult_reserva= "
    SELECT dr.id_detalle_reserva
    FROM Cancha c
    INNER JOIN detalle_reserva dr ON dr.Cancha_id_cancha = c.id_cancha
    WHERE c.id_cancha = {$_POST['id_cancha']}
";

$resultado_consult = mysqli_query($conexion, $consult_reserva);

if (mysqli_num_rows(($resultado_consult)) > 0 ) {
    echo '
        <script>
            alert("No se uede eliminar la cancha porque tiene reservas asociadas.");
            window.locaction = "6_elim_cancha.php";
        </script>
    ';
    exit();
}

mysqli_query($conexion, "DELETE FROM Cancha WHERE id_cancha = {$_POST['id_cancha']}");

if (mysqli_affected_rows(($conexion)) >0 ) {
    echo '
        <script>
            alert("Cancha eliminada correctamente.");
            window.location = "12_gestion_canchas.php";
        </script>
    ';
} else {
    echo '
        <script>
            alert("Error al eliminar la cancha.");
            window.location = "6_elim_cancha.php";
        </script>
    ';
}

mysqli_close($conexion);
?>