<?php
session_start();
if (!isset($_SESSION['correo']) || $_SESSION['rol'] != 'propietario') {
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

//obtener datos del usuario
$res_usuario = mysqli_query($conexion, "SELECT nombre, apellido, dni, correo_electronico FROM Usuario WHERE id_usuario = $id_usuario");
$usuario = mysqli_fetch_assoc($res_usuario);

//Obtener CUIL si existe
$res_cuil = mysqli_query($conexion, "SELECT cuil FROM Propietario WHERE Usuario_id_usuario = $id_usuario");
$datos_prop = mysqli_fetch_assoc($res_cuil);

//Obtenerr ID del propietario
$res_prop = mysqli_query($conexion, "SELECT id_propietario FROM Propietario WHERE Usuario_id_usuario = $id_usuario");
$prop = mysqli_fetch_assoc($res_prop);
$id_propietario = $prop['id_propietario'] ?? null;

// Obtener datos del predio del propietario
$predios = mysqli_query($conexion, "
    SELECT p.nombre, p.descripcion, p.contacto, p.foto_predio,
           u.calle, u.numero, u.ciudad, u.provincia
    FROM Predio p
    JOIN Ubicacion u ON p.Ubicacion_id_ubicacion = u.id_ubicacion
    WHERE p.Propietario_id_propietario = $id_propietario
");

// Obtener canchas
$canchas = mysqli_query($conexion, "
    SELECT c.num_cancha, c.precio_hora, c.tipo_cancha, c.imagen_cancha, p.nombre AS nombre_predio
    FROM Cancha c
    JOIN Predio p ON c.Predio_id_predio = p.id_predio
    WHERE p.Propietario_id_propietario = $id_propietario
");

// Obtener reseñas de los predios del propietario
$resenas = mysqli_query($conexion, "
    SELECT r.fecha_reseña, r.calificacion, r.comentario,
           u.nombre AS nombre_cliente, u.apellido AS apellido_cliente,
           p.nombre AS nombre_predio
    FROM Reseña r
    JOIN Cliente c ON r.Cliente_id_cliente = c.id_cliente
    JOIN Usuario u ON c.Usuario_id_usuario = u.id_usuario
    JOIN Predio p ON r.Predio_id_predio = p.id_predio
    WHERE p.Propietario_id_propietario = $id_propietario
    ORDER BY r.fecha_reseña DESC
    LIMIT 3
");

// Obtener reservas de los predios del propietario
$reservas = mysqli_query($conexion, "
    SELECT 
        r.num_reserva, r.fecha, r.estado_reserva,
        d.hora_inicio, d.hora_salida,
        c.num_cancha,
        u.nombre AS nombre_cliente, u.apellido AS apellido_cliente,
        p.nombre AS nombre_predio
    FROM Reserva r
    JOIN Cliente cli ON r.Cliente_id_cliente = cli.id_cliente
    JOIN Usuario u ON cli.Usuario_id_usuario = u.id_usuario
    JOIN Detalle_Reserva d ON r.id_reserva = d.Reserva_id_reserva
    JOIN Cancha c ON d.Cancha_id_cancha = c.id_cancha
    JOIN Predio p ON c.Predio_id_predio = p.id_predio
    WHERE 
        p.Propietario_id_propietario = $id_propietario AND
        (
            r.fecha > CURDATE() OR 
            (r.fecha = CURDATE() AND d.hora_salida > CURTIME())
        )
    ORDER BY r.fecha ASC, d.hora_inicio ASC
    LIMIT 5
");


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Propietario</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wdth,wght@0,75..100,100..900;1,75..100,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="propietario/estilosPropietario.css">
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <div class="acciones">
            <span>¡Hola <?= $usuario['nombre'] . ' ' . $usuario['apellido'] ?>!</span>
            <a href="propietario/13_gestion_mis_datos.php">Mis datos</a>
            <a href="propietario/11_gestion_predio.php">Gestion de Predio</a>
            <a href="propietario/12_gestion_canchas.php">Gestion de Canchas</a>
                <a href="propietario/14_gestion_horarios.php">Gestionar Horarios</a><br><br>
            <a href="propietario/8_gestion_reservas.php">Reservas</a>
        </div>
        <a href="login/cerrar_sesion.php" class="btn_volver">Cerrar Sesión</a>
    </header>

    <main class="dashboard">
        <!-- Mis Datos -->
        <section class="panel panel-datos">
            <h2>Mis Datos</h2>
            <p><strong>Nombre:</strong> <?= $usuario['nombre'] ?></p>
            <p><strong>Apellido:</strong> <?= $usuario['apellido'] ?></p>
            <p><strong>Correo:</strong> <?= $usuario['correo_electronico'] ?></p>
            <p><strong>Contraseña:</strong> ************</p>
            <p><strong>DNI:</strong> <?= $usuario['dni'] ?></p>
            <p><strong>CUIL:</strong> <?= $datos_prop['cuil'] ?? 'No asignado' ?></p>
            <a href="propietario/13_gestion_mis_datos.php">
                <button>Editar</button>
            </a>
        </section>

        <!-- Mi Predio -->
        <section class="panel panel-predio">
            <h2>Mis Predios</h2>
            <?php 
            if (mysqli_num_rows($predios) > 0):
                $contador = 1;
                while ($p = mysqli_fetch_assoc($predios)) : ?>
                    <div class="predio-detalle">
                        <h4><?= $p['nombre'] ?></h4>
                        <p><strong>Descripción:</strong> <?= $p['descripcion'] ?></p>
                        <p><strong>Contacto:</strong> <?= $p['contacto'] ?></p>
                        <p><strong>Dirección:</strong> <?= "{$p['calle']} {$p['numero']}, {$p['ciudad']}, {$p['provincia']}" ?></p>
                        <?php if (!empty($p['foto_predio'])): ?>
                            <img src="propietario/<?= $p['foto_predio'] ?>" alt="Imagen del predio" class="img-predio">
                        <?php endif; ?>
                        <hr>
                    </div>
            <?php 
                $contador++;
                endwhile;
            else: ?>
                <p>No hay predios registrados.</p>
            <?php endif; ?>
            <a href="propietario/11_gestion_predio.php">
                <button>Administrar</button>
            </a>
        </section>

        <!-- Canchas -->
        <section class="panel panel-canchas">
            <h2>Canchas</h2>
            <?php while ($c = mysqli_fetch_assoc($canchas)): ?>
                <div class="cancha-card">
                    <img src="propietario/<?= $c['imagen_cancha'] ?>" alt="Imagen de cancha">
                    <p><strong>Cancha N<?= $c['num_cancha'] ?></strong></p>
                    <p><?= $c['tipo_cancha'] ?> - $<?= $c['precio_hora'] ?>/H</p>
                    <p class="predio_canchas"><strong><?= $c['nombre_predio'] ?></strong></p>
                </div>
            <?php endwhile; ?>
        </section>

        <!-- Reservas -->
        <section class="panel panel-reservas">
            <h2>Reservas</h2>
            <?php while ($res = mysqli_fetch_assoc($reservas)): ?>
                <div class="reserva-item">
                    <p class="reserva-num"><strong>🔢 Nº de Reserva:</strong> <?= $res['num_reserva'] ?></p>
                    <p class="reserva-predio"><strong>🏟️ Predio:</strong> <?= $res['nombre_predio'] ?></p>
                    <p class="reserva-cancha"><strong>⚽ Cancha:</strong> <?= $res['num_cancha'] ?></p><br>
                    <p class="reserva-cliente"><strong>👤 Cliente:</strong> <?= $res['nombre_cliente'] . ' ' . $res['apellido_cliente'] ?></p><br>
                    <p class="reserva-fecha"><strong>📅 Fecha:</strong> <?= $res['fecha'] ?></p>
                    <p class="reserva-hora"><strong>⏰ Hora:</strong> <?= $res['hora_inicio'] ?> a <?= $res['hora_salida'] ?></p><br>
                    <p class="reserva-estado"><strong>📌 Estado:</strong> <?= ucfirst($res['estado_reserva']) ?></p>
                    <hr>
                </div>
            <?php endwhile; ?>
        </section>

        <!-- Reseñas -->
        <section class="panel panel-resenas">
            <h2>Reseñas</h2>

            <?php if (mysqli_num_rows($resenas) > 0): ?>
                <?php while ($r = mysqli_fetch_assoc($resenas)): ?>
                    <div class="resena-item">
                        <p><strong>Predio:</strong> <?= $r['nombre_predio'] ?></p>
                        <p><strong>Cliente:</strong> <?= $r['nombre_cliente'] . ' ' . $r['apellido_cliente'] ?></p>
                        <p><strong>Fecha:</strong> <?= $r['fecha_reseña'] ?></p>
                        <p><strong>Calificación:</strong> <?= $r['calificacion'] ?>/10</p>
                        <p><strong>Comentario:</strong> <?= $r['comentario'] ?: 'Sin comentario.' ?></p>
                        <hr>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay reseñas aún.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
