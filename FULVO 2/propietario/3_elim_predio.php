<?php
session_start();
include '../login/conexion_be.php';

// Verificación de sesión
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

// Obtener ID del propietario
$id_usuario = $_SESSION['id_usuario'];
$query_prop = mysqli_query($conexion, "SELECT id_propietario FROM propietario WHERE Usuario_id_usuario = '$id_usuario'");
$prop = mysqli_fetch_assoc($query_prop);
$id_propietario = $prop['id_propietario'] ?? null;

$predios = [];
if ($id_propietario) {
    $predios_query = mysqli_query($conexion, "SELECT id_predio, nombre FROM predio WHERE Propietario_id_propietario = '$id_propietario'");
    while ($row = mysqli_fetch_assoc($predios_query)) {
        $predios[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Predio</title>
    <link rel="stylesheet" href="estilosPropietario.css">
</head>
<body>
<header class="barra_superior">
    <h1>FULVO.COM</h1>
    <a href="11_gestion_predio.php" class="btn_volver">Volver</a>
</header>
<main>
    <div class="form_alta_predio">
        <h3>Eliminar Predio</h3>
        <form action="3_elim_predio_be.php" method="POST">
            <label for="id_predio">Seleccioná un predio para eliminar:</label><br>
            <select name="id_predio" required>
                <option value="">-- Seleccionar predio --</option>
                <?php foreach ($predios as $predio): ?>
                    <option value="<?= $predio['id_predio'] ?>"><?= $predio['nombre'] ?></option>
                <?php endforeach; ?>
            </select><br><br>
            <button type="submit" name="eliminar">Eliminar Predio</button>
        </form>
    </div>
</main>
</body>
</html>
