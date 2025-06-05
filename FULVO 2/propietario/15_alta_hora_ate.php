<?php
session_start();
include '../login/conexion_be.php';

$id_usuario = $_SESSION['id_usuario'];

if (!isset($_SESSION['correo']) || $_SESSION['rol'] != 'propietario') {
    echo '<script>alert("Acceso denegado."); window.location = "../login/login_registro.php";</script>';
    exit();
}

// Obtener los predios del propietario
$predios = mysqli_query($conexion, "
    SELECT p.id_predio, p.nombre
    FROM Predio p
    JOIN Propietario prop ON p.Propietario_id_propietario = prop.id_propietario
    WHERE prop.Usuario_id_usuario = $id_usuario
");

// Obtener los días desde la tabla Dias
$dias = mysqli_query($conexion, "SELECT * FROM Dias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cargar Horarios de Atención</title>
    <link rel="stylesheet" href="estilosPropietario.css">
</head>
<body>
<header class="barra_superior">
    <h1>FULVO.COM</h1>
    <a href="14_gestion_horarios.php" class="btn_volver">Volver</a>
</header>

<main>
    <div class="form_alta_predio">
        <h3>Asignar Horario a Predio</h3>
        <form action="15_alta_hora_ate_be.php" method="post">
            <label>Predio:</label>
            <select name="id_predio" required>
                <option value="">-- Seleccionar --</option>
                <?php while ($p = mysqli_fetch_assoc($predios)) {
                    echo "<option value='{$p['id_predio']}'>{$p['nombre']}</option>";
                } ?>
            </select><br><br>

            <div id="contenedor_horarios">
                <div class="grupo_dia">
                    <select name="dias[]">
                        <?php while ($d = mysqli_fetch_assoc($dias)) {
                            echo "<option value='{$d['id_dias']}'>{$d['nombre']}</option>";
                        } ?>
                    </select>
                    <input type="time" name="aperturas[]" required>
                    <input type="time" name="cierres[]" required>
                </div>
            </div>
            <br>
            <button type="button" onclick="agregarDia()">+ Añadir otro día</button><br><br>
            <button type="submit">Guardar Horarios</button>
        </form>
    </div>
</main>

<script>
function agregarDia() {
    const contenedor = document.getElementById("contenedor_horarios");
    const grupo = document.createElement("div");
    grupo.classList.add("grupo_dia");
    grupo.innerHTML = `
        <select name="dias[]">
            <option value="1">Lunes</option>
            <option value="2">Martes</option>
            <option value="3">Miércoles</option>
            <option value="4">Jueves</option>
            <option value="5">Viernes</option>
            <option value="6">Sábado</option>
            <option value="7">Domingo</option>
        </select>
        <input type="time" name="aperturas[]" required>
        <input type="time" name="cierres[]" required>
        <button type="button" onclick="this.parentElement.remove()">Eliminar</button>
    `;
    contenedor.appendChild(grupo);
}
</script>
</body>
</html>
