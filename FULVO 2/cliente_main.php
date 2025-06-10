<?php
session_start();

if (!isset($_SESSION['correo']) || $_SESSION['rol'] != 'cliente') {
    echo '
        <script>
            alert("Debes iniciar sesión.");
            window.location = "login_registro.php";
        </script>
    ';
    session_destroy();
    die();
}

include 'login/conexion_be.php';
$id_usuario = $_SESSION['id_usuario'];

// Obtener nombre y apellido del cliente
$res_cliente = mysqli_query($conexion, "SELECT nombre, apellido FROM Usuario WHERE id_usuario = $id_usuario");
$cliente = mysqli_fetch_assoc($res_cliente);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Cliente</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wdth,wght@0,75..100,100..900;1,75..100,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="cliente/estilosclientes.css">
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <div class="acciones">
            <span>¡Hola <?= $cliente['nombre'] . ' ' . $cliente['apellido'] ?>!</span>
            <a href="cliente/mis_datos.php">Mis datos</a>
            <a href="cliente/mis_reservas.php">Mis reservas</a>
            <a href="cliente/mis_resenas.php">Mis reseñas</a>
        </div>
        <a href="login/cerrar_sesion.php" class="btn_volver">Cerrar sesión</a>
    </header>

    <main class="busqueda_reserva">
        <h2>Reservar Cancha</h2>
        <form action="" method="POST" class="form-busqueda">
            <label for="fecha">Seleccioná una fecha:</label>
            <input type="date" id="fecha" name="fecha" required min="<?php echo date('Y-m-d'); ?>">
            <button type="submit">Buscar Predios Disponibles</button>
        </form>

        <?php
        if (isset($_POST['fecha'])) {
            $fecha = $_POST['fecha'];
            $dia_semana = date('l', strtotime($fecha));

            $dias_traducidos = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes',
                'Wednesday' => 'Miercoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            $dia_es = $dias_traducidos[$dia_semana] ?? '';

            $consulta = "
                SELECT p.id_predio, p.nombre, p.descripcion, p.contacto, p.foto_predio,
                    u.calle, u.numero, u.ciudad, u.provincia,
                    h.horario_apertura, h.horario_cierre
                FROM Predio p
                JOIN Horario_Atencion h ON p.id_predio = h.Predio_id_predio
                JOIN Dias d ON h.Dias_id_dias = d.id_dias
                JOIN Ubicacion u ON p.Ubicacion_id_ubicacion = u.id_ubicacion
                WHERE d.nombre = '$dia_es'
            ";

            $resultado = mysqli_query($conexion, $consulta);

            if (mysqli_num_rows($resultado) > 0): ?>
                <section class="panel-resultados">
                    <h3>Predios disponibles el <?= $dia_es ?> <?= $fecha ?>:</h3>
                    <div class="cards-container">
                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <div class="card-predio-horizontal">
                            <img src="propietario/<?= $fila['foto_predio'] ?>" alt="Imagen del predio">
                            <div class="card-info">
                                <h4><?= $fila['nombre'] ?></h4>
                                <p><?= $fila['descripcion'] ?></p>
                                <p><strong>📍 Dirección:</strong> <?= "{$fila['calle']} {$fila['numero']}, {$fila['ciudad']}, {$fila['provincia']}" ?></p>
                                <p><strong>📞 Contacto:</strong> <?= $fila['contacto'] ?></p>
                                <p><strong>🕒 Horario:</strong> <?= "{$fila['horario_apertura']} - {$fila['horario_cierre']}" ?></p>
                                <a href="cliente/1_alta_reserva.php?id=<?= $fila['id_predio'] ?>" class="btn-seleccionar">Ver más</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    </div>
                </section>
            <?php else:
                echo "<p class='no-resultados'>No hay predios disponibles para esa fecha.</p>";
            endif;
        }
        ?>
    </main>

</body>
</html>
