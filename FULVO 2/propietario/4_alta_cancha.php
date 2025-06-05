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

// Obtener predios del propietario para el select
$predios = mysqli_query($conexion, "SELECT id_predio, nombre FROM Predio WHERE Propietario_id_propietario = $id_propietario");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alta de Cancha</title>
    <link rel="stylesheet" href="estilosPropietario.css">
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <a href="12_gestion_canchas.php" class="btn_volver">Volver</a>
    </header>

    <main>
        <div class="form_alta_predio">
            <h3>Registrar Nueva Cancha</h3>
            <form action="4_alta_cancha_be.php" method="POST" enctype="multipart/form-data">
                <input type="number" name="num_cancha" placeholder="Número de cancha" required><br>
                <input type="number" step="0.01" name="precio_hora" placeholder="Precio por hora" required><br>
                <input type="number" name="capacidad" placeholder="Capacidad" required><br>

                <label for="tipo_cancha">Tipo de cancha:</label><br>
                <select name="tipo_cancha" required>
                    <option value="Cesped">Cesped</option>
                    <option value="Sintetico">Sintetico</option>
                    <option value="Piso">Piso</option>
                </select><br>

                <label for="disponibilidad">Disponibilidad:</label><br>
                <select name="disponibilidad" required>
                    <option value="libre">Libre</option>
                    <option value="ocupada">Ocupada</option>
                </select><br>

                <label for="imagen_cancha">Imagen de la cancha:</label><br>
                <input type="file" name="imagen_cancha" accept="image/*"><br><br>

                <label for="predio_id">Seleccionar Predio:</label><br>
                <select name="predio_id" required>
                    <option value="">-- Seleccionar --</option>
                    <?php while ($p = mysqli_fetch_assoc($predios)) {
                        echo "<option value='{$p['id_predio']}'>{$p['nombre']}</option>";
                    } ?>
                </select><br><br>

                <button type="submit">Registrar Cancha</button>
            </form>
        </div>
    </main>
</body>
</html>
