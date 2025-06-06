<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';

$id_usuario = $_SESSION['id_usuario'];

// Obtener nombre del propietario
$res_usuario = mysqli_query($conexion, "SELECT nombre, apellido FROM Usuario WHERE id_usuario = $id_usuario");
$usuario = mysqli_fetch_assoc($res_usuario);

// Obtener ID del propietario
$res_prop = mysqli_query($conexion, "SELECT id_propietario FROM Propietario WHERE Usuario_id_usuario = $id_usuario");
$prop = mysqli_fetch_assoc($res_prop);
$id_propietario = $prop['id_propietario'] ?? null;

// Obtener todas las canchas del propietario
$canchas = mysqli_query($conexion, "
    SELECT c.*, p.nombre AS nombre_predio
    FROM Cancha c
    JOIN Predio p ON c.Predio_id_predio = p.id_predio
    WHERE p.Propietario_id_propietario = $id_propietario
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Canchas</title>
    <link rel="stylesheet" href="estilosPropietario.css">
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <div class="acciones-contenedor">
            <span>¡Acá podés gestionar todas tus canchas!</span>
            <div class="acciones">
                <a href="4_alta_cancha.php">Registrar Cancha</a>
                <a href="5_actu_cancha.php">Modificar Cancha</a>
                <a href="6_elim_cancha.php">Eliminar Cancha</a>
            </div>
        </div>
        <a href="../propietario_main.php" class="btn_volver">Volver</a>
    </header>

    <main>
        <?php 
            $contador = 1;
            while ($cancha = mysqli_fetch_assoc($canchas)) { 
        ?>
            <div class="predio-card">
                <h2>Cancha <?= $contador ?> - <?= $cancha['nombre_predio'] ?></h2>
                <p><strong>Número:</strong> <?= $cancha['num_cancha'] ?></p>
                <p><strong>Precio por hora:</strong> $<?= $cancha['precio_hora'] ?></p>
                <p><strong>Capacidad:</strong> <?= $cancha['capacidad'] ?></p>
                <p><strong>Tipo:</strong> <?= $cancha['tipo_cancha'] ?></p>
                <p><strong>Disponibilidad:</strong> <?= ucfirst($cancha['disponibilidad']) ?></p>
                <?php if ($cancha['imagen_cancha']): ?>
                    <p><img src="<?= $cancha['imagen_cancha'] ?>" alt="Imagen de la cancha" style="max-width:300px;"></p>
                <?php endif; ?>
            </div>
        <?php 
            $contador++;
            } 
        ?>
    </main>
</body>
</html>