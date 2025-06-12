<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';
$id_usuario = $_SESSION['id_usuario'];

$res_usuario = mysqli_query($conexion, "SELECT nombre, apellido, dni, correo_electronico FROM Usuario WHERE id_usuario = $id_usuario");
$usuario = mysqli_fetch_assoc($res_usuario);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Mis Datos</title>
    <link rel="stylesheet" href="estilosClientes.css">
</head>
<body>
<header class="barra_superior">
    <h1>FULVO.COM</h1>
    <span>¡Aca podes editar tus datos si deseas!</span>
    <a href="../cliente_main.php" class="btn_volver">Volver</a>
</header>
<main>
    <div class="card_reserva">
        <h3>Editar Mis Datos</h3>
        <form action="13_gestion_mis_datos_be.php" method="POST">
            <input type="text" name="nombre" value="<?= $usuario['nombre'] ?>" required><br>
            <input type="text" name="apellido" value="<?= $usuario['apellido'] ?>" required><br>
            <input type="email" name="correo" value="<?= $usuario['correo_electronico'] ?>" required><br>
            <input type="number" name="dni" value="<?= $usuario['dni'] ?>" required><br>
            <input type="password" name="contrasena" placeholder="Nueva Contraseña (opcional)"><br>
            <button type="submit">Actualizar Datos</button>
        </form>
    </div>
</main>
</body>
</html>