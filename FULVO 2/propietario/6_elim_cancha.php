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

// Obtener canchas del propietario
$canchas = mysqli_query($conexion, "
    SELECT c.id_cancha, c.num_cancha, p.nombre AS predio_nombre
    FROM Cancha c
    JOIN Predio p ON c.Predio_id_predio = p.id_predio
    WHERE p.Propietario_id_propietario = $id_propietario
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Cancha</title>
    <link rel="stylesheet" href="estilosPropietario.css">
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <a href="12_gestion_canchas.php" class="btn_volver">Volver</a>
    </header>

    <main class="form_alta_predio">
        <h3>Eliminar Cancha</h3>
        <form method="POST" action="6_elim_cancha_be.php" onsubmit="return confirmarEliminacion()">
            <label for="id_cancha">Selecciona la cancha a eliminar:</label><br>
            <select name="id_cancha" required>
                <option value="">-- Seleccionar cancha --</option>
                <?php while ($c = mysqli_fetch_assoc($canchas)): ?>
                    <option value="<?= $c['id_cancha'] ?>">
                        <?= "Cancha N°{$c['num_cancha']} - Predio: {$c['predio_nombre']}" ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>
            <button type="submit">Eliminar</button>
        </form>
    </main>

    <script>
        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar esta cancha?");
        }
    </script>
</body>
</html>