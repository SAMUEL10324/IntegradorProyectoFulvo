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

// Obtener todos los predios de este propietario
$predios = mysqli_query($conexion, "
    SELECT p.*, u.calle, u.numero, u.ciudad, u.provincia
    FROM Predio p
    JOIN Ubicacion u ON p.Ubicacion_id_ubicacion = u.id_ubicacion
    WHERE p.Propietario_id_propietario = $id_propietario
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Predios</title>
    <link rel="stylesheet" href="estilosPropietario.css">
    <style>
        
    </style>
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <div class="acciones-contenedor">
            <span>¡Acá podes gestionar todos tus predios!</span>
            <div class="acciones">
                <a href="1_alta_predio.php">Registrar Predio</a>
                <a href="2_actu_predio.php">Modificar Predio</a>
                <a href="3_elim_predio.php">Eliminar Predio</a>
            <div>
        </div>
        <a href="../propietario_main.php" class="btn_volver">Volver</a>
    </header>

    <main>
        <?php 
            $contador = 1;
            while ($predio = mysqli_fetch_assoc($predios)) { 
        ?>
            <div class="predio-card">
                <h2>Mi Predio <?= $contador?></h2>
                <p><strong>Nombre:</strong> <?= $predio['nombre'] ?></p>
                <p><strong>Descripción:</strong> <?= $predio['descripcion'] ?></p>
                <p><strong>Contacto:</strong> <?= $predio['contacto'] ?></p>
                <p><strong>Imagen:</strong><br>
                    <img src="<?= $predio['foto_predio'] ?>" alt="Imagen del predio">
                </p>
                <p><strong>Calle:</strong> <?= $predio['calle'] ?></p>
                <p><strong>Número de calle:</strong> <?= $predio['numero'] ?></p>
                <p><strong>Ciudad:</strong> <?= $predio['ciudad'] ?></p>
                <p><strong>Provincia:</strong> <?= $predio['provincia'] ?></p>
            </div>
        <?php 
            $contador++;
        } ?>
    </main>
</body>
</html>

