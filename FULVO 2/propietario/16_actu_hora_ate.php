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

// Obtener días
$dias = mysqli_query($conexion, "SELECT id_dias, nombre FROM Dias");

// Inicializar variables
$horarios = [];
$predio_id = null;

if (isset($_POST['seleccionar_predio'])) {
    $predio_id = $_POST['predio_id'];
    $horarios_query = mysqli_query($conexion, "
        SELECT h.id_horarios_atencion, h.horario_apertura, h.horario_cierre, d.nombre AS dia, d.id_dias
        FROM Horario_Atencion h
        JOIN Dias d ON h.Dias_id_dias = d.id_dias
        WHERE h.Predio_id_predio = $predio_id
    ");
    while ($row = mysqli_fetch_assoc($horarios_query)) {
        $horarios[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Horarios</title>
    <link rel="stylesheet" href="estilosPropietario.css">
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <a href="14_gestion_horarios.php" class="btn_volver">Volver</a>
    </header>

    <main>
        <div class="form_alta_predio">
            <h3>Modificar Horarios</h3>
            <form method="POST">
                <label for="predio_id">Seleccionar Predio:</label><br>
                <select name="predio_id" required>
                    <option value="">-- Seleccione un predio --</option>
                    <?php while ($p = mysqli_fetch_assoc($predios)) {
                        $selected = (isset($predio_id) && $p['id_predio'] == $predio_id) ? 'selected' : '';
                        echo "<option value='{$p['id_predio']}' $selected>{$p['nombre']}</option>";
                    } ?>
                </select>
                <button type="submit" name="seleccionar_predio">Cargar horarios</button>
            </form>

            <?php if (!empty($horarios)) { ?>
                <form method="POST" action="16_actu_hora_ate_be.php">
                    <input type="hidden" name="predio_id" value="<?= $predio_id ?>">

                    <label for="id_horarios_atencion">Seleccionar horario actual:</label>
                    <select name="id_horarios_atencion" required>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($horarios as $h) {
                            echo "<option value='{$h['id_horarios_atencion']}'>
                                    Día: {$h['dia']} | {$h['horario_apertura']} - {$h['horario_cierre']}
                                  </option>";
                        } ?>
                    </select><br><br>

                    <label for="nuevo_dia">Nuevo Día:</label><br>
                    <select name="nuevo_dia" required>
                        <option value="">-- Seleccione un día --</option>
                        <?php mysqli_data_seek($dias, 0); while ($d = mysqli_fetch_assoc($dias)) {
                            echo "<option value='{$d['id_dias']}'>{$d['nombre']}</option>";
                        } ?>
                    </select><br>

                    <label for="nuevo_apertura">Nuevo horario de apertura:</label><br>
                    <input type="time" name="nuevo_apertura" required><br>

                    <label for="nuevo_cierre">Nuevo horario de cierre:</label><br>
                    <input type="time" name="nuevo_cierre" required><br>

                    <button type="submit">Modificar Horario</button>
                </form>
            <?php } ?>
        </div>
    </main>
</body>
</html>

</body>
</html>
