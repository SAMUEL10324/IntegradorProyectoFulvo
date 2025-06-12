<?php
session_start();
include '../login/conexion_be.php';

// Validar que el cliente esté logueado
if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'cliente') {
    echo '<script>alert("Debes iniciar sesión."); window.location = "../login_registro.php";</script>';
    session_destroy();
    die();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alta Reseña</title>
    <link rel="stylesheet" href="estilosclientes.css">
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <a href="5_gestion_reseña.php" class="btn_volver">Volver</a>
    </header>

    <main>
        <div class="form_alta_resena">
            <h3>Escribir Reseña</h3>
            <form action="3_alta_reseña_be.php" method="post">
                
                <!-- Seleccionar Predio -->
                <label for="predio">Predio:</label>
                <select name="predio_id" id="predio" required>
                    <?php
                    $consulta = "SELECT id_predio, nombre FROM Predio ORDER BY nombre ASC";
                    $resultado = mysqli_query($conexion, $consulta);
                    while ($fila = mysqli_fetch_assoc($resultado)) {
                        echo "<option value='{$fila['id_predio']}'>{$fila['nombre']}</option>";
                    }
                    ?>
                </select>

                <!-- Calificación -->
                <label for="calificacion">Calificación (1 a 10):</label>
                <input type="number" name="calificacion" min="1" max="10" step="1" required>

                <!-- Comentario -->
                <label for="comentario">Comentario:</label>
                <textarea name="comentario" rows="4" placeholder="Escribe tu reseña aquí..." required></textarea>

                <!-- Botón -->
                <button type="submit">Enviar Reseña</button>
            </form>
        </div>
    </main>
</body>
</html>

