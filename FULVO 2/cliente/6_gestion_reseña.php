<?php
session_start();
include '../login/conexion_be.php';

if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login_registro.php");
    exit();
}

// Obtener datos del cliente
$correo = $_SESSION['correo'];
$consulta_usuario = "SELECT u.id_usuario, u.nombre, u.apellido, c.id_cliente
                     FROM Usuario u
                     JOIN Cliente c ON u.id_usuario = c.Usuario_id_usuario
                     WHERE u.correo_electronico = '$correo'";
$resultado_usuario = mysqli_query($conexion, $consulta_usuario);
$fila_usuario = mysqli_fetch_assoc($resultado_usuario);
$id_cliente = $fila_usuario['id_cliente'] ?? null;
$nombre = $fila_usuario['nombre'] ?? '';
$apellido = $fila_usuario['apellido'] ?? '';

if (!$id_cliente) {
    echo '<script>alert("No se pudo obtener el ID del cliente."); window.location = "cliente_main.php";</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reseñas</title>
    <link rel="stylesheet" href="estilosClientes.css">
</head>
<body>

<header class="barra_superior">
    <h1>FULVO.COM</h1>
    <div class="acciones">
        <span>¡Aqui puedes ver tus Reseñas y publicar nuevas!</span>
        <a href="3_alta_reseña.php">Nueva reseña</a>
    </div>
    <a href="../cliente_main.php" class="btn_volver">Volver</a>
</header>

<main>
    <div class="panel-resultados">
        <h2>Mis Reseñas</h2>

        <?php
        $consulta = "SELECT r.id_reseña, r.fecha_reseña, r.calificacion, r.comentario, p.nombre AS nombre_predio
                     FROM Reseña r
                     JOIN Predio p ON r.Predio_id_predio = p.id_predio
                     WHERE r.Cliente_id_cliente = $id_cliente
                     ORDER BY r.fecha_reseña DESC";

        $resultado = mysqli_query($conexion, $consulta);

        if (mysqli_num_rows($resultado) > 0) {
            echo '<div class="cards-container">';
            while ($fila = mysqli_fetch_assoc($resultado)) {
                echo '
                    <div class="cancha-card">
                        <div class="cancha-info">
                            <h3>' . htmlspecialchars($fila['nombre_predio']) . '</h3>
                            <p><strong>Fecha:</strong> ' . $fila['fecha_reseña'] . '</p>
                            <p><strong>Calificación:</strong> ' . $fila['calificacion'] . '/10</p>
                            <p><strong>Comentario:</strong> ' . htmlspecialchars($fila['comentario']) . '</p>
                            <form action="4_elim_reseña_be.php" method="post" onsubmit="return confirm(\'¿Estás seguro que querés eliminar esta reseña?\')">
                                <input type="hidden" name="id_reseña" value="' . $fila['id_reseña'] . '">
                                <button type="submit" class="btn_volver">Eliminar</button>
                            </form>
                        </div>
                    </div>
                ';
            }
            echo '</div>';
        } else {
            echo '<div class="no-resultados">Aún no realizaste ninguna reseña.</div>';
        }
        ?>
    </div>
</main>
</body>
</html>
