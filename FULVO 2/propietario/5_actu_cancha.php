<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'propietario') {
    header("Location: ../login_registro.php");
    exit();
}

include '../login/conexion_be.php';
$id_usuario = $_SESSION['id_usuario'];

// Obtener ID del propietario
$res_prop = mysqli_query($conexion, "SELECT id_propietario FROM Propietario WHERE Usuario_id_usuario = $id_usuario");
$prop = mysqli_fetch_assoc($res_prop);
$id_propietario = $prop['id_propietario'] ?? null;

// Obtener predios
$predios = mysqli_query($conexion, "SELECT id_predio, nombre FROM Predio WHERE Propietario_id_propietario = $id_propietario");

$cancha_seleccionada = null;
if (isset($_POST['buscar_canchas'])) {
    $predio_id = $_POST['predio_id'];
    $canchas = mysqli_query($conexion, "SELECT * FROM Cancha WHERE Predio_id_predio = $predio_id");
}

if (isset($_POST['editar_cancha'])) {
    $id_cancha = $_POST['id_cancha'];
    $cancha_query = mysqli_query($conexion, "SELECT * FROM Cancha WHERE id_cancha = $id_cancha");
    $cancha_seleccionada = mysqli_fetch_assoc($cancha_query);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Cancha</title>
    <link rel="stylesheet" href="estilosPropietario.css">
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <a href="12_gestion_canchas.php" class="btn_volver">Volver</a>
    </header>

    <main>
        <div class="form_alta_predio">
            <h3>Actualizar Cancha</h3>

            <!-- Selección de predio -->
            <form method="POST">
                <label>Seleccionar predio:</label><br>
                <select name="predio_id" required>
                    <option value="">-- Seleccione --</option>
                    <?php while ($p = mysqli_fetch_assoc($predios)) {
                        $sel = (isset($predio_id) && $p['id_predio'] == $predio_id) ? 'selected' : '';
                        echo "<option value='{$p['id_predio']}' $sel>{$p['nombre']}</option>";
                    } ?>
                </select>
                <button type="submit" name="buscar_canchas">Buscar canchas</button>
            </form>

            <?php if (isset($canchas)): ?>
                <form method="POST">
                    <label>Seleccionar cancha:</label><br>
                    <select name="id_cancha" required>
                        <option value="">-- Seleccione --</option>
                        <?php while ($c = mysqli_fetch_assoc($canchas)) {
                            echo "<option value='{$c['id_cancha']}'>Cancha N° {$c['num_cancha']}</option>";
                        } ?>
                    </select>
                    <button type="submit" name="editar_cancha">Editar Cancha</button>
                </form>
            <?php endif; ?>

            <?php if ($cancha_seleccionada): ?>
                <form action="5_actu_cancha_be.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_cancha" value="<?= $cancha_seleccionada['id_cancha'] ?>">

                    <label>Número de cancha:</label>
                    <input type="number" name="num_cancha" value="<?= $cancha_seleccionada['num_cancha'] ?>" required><br>

                    <label>Precio por hora:</label>
                    <input type="number" step="0.01" name="precio_hora" value="<?= $cancha_seleccionada['precio_hora'] ?>" required><br>

                    <label>Capacidad:</label>
                    <input type="number" name="capacidad" value="<?= $cancha_seleccionada['capacidad'] ?>"><br>

                    <label for="tipo_cancha">Tipo de cancha:</label><br>
                    <select name="tipo_cancha" required>
                    <option value="Cesped">Cesped</option>
                    <option value="Sintetico">Sintetico</option>
                    <option value="Piso">Piso</option>
                    </select><br>

                    <label>Estado:</label>
                    <select name="disponibilidad" required>
                        <option value="libre" <?= $cancha_seleccionada['disponibilidad'] === 'libre' ? 'selected' : '' ?>>Libre</option>
                        <option value="ocupada" <?= $cancha_seleccionada['disponibilidad'] === 'ocupada' ? 'selected' : '' ?>>Ocupada</option>
                    </select><br>

                    <label>Imagen actual:</label><br>
                    <?php if (!empty($cancha_seleccionada['imagen_cancha'])): ?>
                        <img src="<?= $cancha_seleccionada['imagen_cancha'] ?>" alt="Cancha" style="max-width: 200px;"><br>
                    <?php endif; ?>

                    <label>Cambiar imagen:</label>
                    <input type="file" name="imagen_cancha"><br>

                    <button type="submit">Actualizar Cancha</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>