<?php
session_start();
if(!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario'){
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';

$id_usuario = $_SESSION['id_usuario'];

// Buscar ID del propietario
$res_prop = mysqli_query($conexion, "SELECT id_propietario FROM Propietario WHERE Usuario_id_usuario = $id_usuario");
$prop = mysqli_fetch_assoc($res_prop);
$id_propietario = $prop['id_propietario'] ?? null;

$predioSeleccionado = null;
if (isset($_POST['cargar_predio'])) {
    $id = $_POST['id_predio'];
    $res = mysqli_query($conexion, "
        SELECT p.*, u.calle, u.numero, u.ciudad, u.provincia
        FROM Predio p
        JOIN Ubicacion u ON p.Ubicacion_id_ubicacion = u.id_ubicacion
        WHERE p.id_predio = $id
    ");
    $predioSeleccionado = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Predio</title>
    <link rel="stylesheet" href="estilosPropietario.css">
</head>
<body>
<header class="barra_superior">
    <h1>FULVO.COM</h1>
    <div class="acciones">
        <a href="11_gestion_predio.php" class="btn_volver">Volver</a>
    </div>
</header>

<main>
    <div class="form_alta_predio">
        <h3>Seleccionar Predio</h3>
        <form method="POST" action="">
            <select name="id_predio" required>
                <option value="">-- Elegí un predio --</option>
                <?php
                $res = mysqli_query($conexion, "SELECT id_predio, nombre FROM Predio WHERE Propietario_id_propietario = $id_propietario");
                while ($row = mysqli_fetch_assoc($res)) {
                    $selected = (isset($_POST['id_predio']) && $_POST['id_predio'] == $row['id_predio']) ? 'selected' : '';
                    echo "<option value='{$row['id_predio']}' $selected>{$row['nombre']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="cargar_predio">Cargar</button>
        </form>

        <?php if ($predioSeleccionado) { ?>
            <h3>Editar Predio</h3>
            <form method="POST" action="2_actu_predio_be.php" enctype="multipart/form-data">
                <input type="hidden" name="id_predio" value="<?= $predioSeleccionado['id_predio'] ?>">
                <input type="hidden" name="id_ubicacion" value="<?= $predioSeleccionado['Ubicacion_id_ubicacion'] ?>">

                <input type="text" name="nombre" placeholder="Nombre" value="<?= $predioSeleccionado['nombre'] ?>" required><br>
                <input type="text" name="descripcion" placeholder="Descripción" value="<?= $predioSeleccionado['descripcion'] ?>" required><br>
                <input type="text" name="contacto" placeholder="Contacto" value="<?= $predioSeleccionado['contacto'] ?>" required><br>

                <p>Imagen actual:</p>
                <img src="<?= $predioSeleccionado['foto_predio'] ?>" style="max-width: 250px;"><br>
                <input type="file" name="foto_predio_nueva" accept="image/*"><br>

                <input type="text" name="calle" placeholder="Calle" value="<?= $predioSeleccionado['calle'] ?>" required><br>
                <input type="text" name="numero" placeholder="Número" value="<?= $predioSeleccionado['numero'] ?>" required><br>
                <input type="text" name="ciudad" placeholder="Ciudad" value="<?= $predioSeleccionado['ciudad'] ?>" required><br>
                <input type="text" name="provincia" placeholder="Provincia" value="<?= $predioSeleccionado['provincia'] ?>" required><br>

                <button type="submit" name="editar">Guardar Cambios</button>
            </form>
        <?php } ?>
    </div>
</main>
</body>
</html>

