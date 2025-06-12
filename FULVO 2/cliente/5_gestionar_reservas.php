<?php
session_start();
include '../login/conexion_be.php';

if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'cliente') {
    echo '<script>alert("Debes iniciar sesión."); window.location = "../login_registro.php";</script>';
    session_destroy();
    die();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener el ID del cliente
$res = mysqli_query($conexion, "SELECT id_cliente FROM Cliente WHERE Usuario_id_usuario = $id_usuario");
$cliente = mysqli_fetch_assoc($res);
$id_cliente = $cliente['id_cliente'] ?? null;

if (!$id_cliente) {
    echo '<script>alert("No se encontró el cliente."); window.location = "../cliente_main.php";</script>';
    exit;
}

// Obtener reservas y detalles relacionados
$reservas = mysqli_query($conexion, "
    SELECT 
        r.id_reserva, r.fecha, r.estado_reserva, r.num_reserva,
        c.id_cancha, c.num_cancha, c.tipo_cancha, c.precio_hora, c.imagen_cancha,
        p.nombre AS nombre_predio, p.contacto, u.calle, u.numero, u.ciudad, u.provincia,
        dr.hora_inicio, dr.hora_salida
    FROM Reserva r
    JOIN Detalle_Reserva dr ON r.id_reserva = dr.Reserva_id_reserva
    JOIN Cancha c ON dr.Cancha_id_cancha = c.id_cancha
    JOIN Predio p ON c.Predio_id_predio = p.id_predio
    JOIN Ubicacion u ON p.Ubicacion_id_ubicacion = u.id_ubicacion
    WHERE r.Cliente_id_cliente = $id_cliente
    ORDER BY r.fecha DESC, r.id_reserva DESC, dr.hora_inicio
");

$reservas_array = [];
while ($fila = mysqli_fetch_assoc($reservas)) {
    $id_reserva = $fila['id_reserva'];
    if (!isset($reservas_array[$id_reserva])) {
        $reservas_array[$id_reserva] = [
            'fecha' => $fila['fecha'],
            'estado' => $fila['estado_reserva'],
            'num_reserva' => $fila['num_reserva'],
            'predio' => [
                'nombre' => $fila['nombre_predio'],
                'contacto' => $fila['contacto'],
                'direccion' => "{$fila['calle']} {$fila['numero']}, {$fila['ciudad']}, {$fila['provincia']}"
            ],
            'cancha' => [
                'num_cancha' => $fila['num_cancha'],
                'tipo' => $fila['tipo_cancha'],
                'precio' => $fila['precio_hora'],
                'imagen' => $fila['imagen_cancha']
            ],
            'horarios' => []
        ];
    }
    $reservas_array[$id_reserva]['horarios'][] = "{$fila['hora_inicio']} - {$fila['hora_salida']}";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas</title>
    <link rel="stylesheet" href="estilosClientes.css">
</head>
<body>
<header class="barra_superior">
    <h1>FULVO.COM</h1>
    <a href="../cliente_main.php" class="btn_volver">Volver</a>
</header>
<main>
    <?php if (count($reservas_array) > 0): ?>
        <?php foreach ($reservas_array as $id => $reserva): ?>
            <div class="card-reserva">
                <h3>Reserva Nº <?= $reserva['num_reserva'] ?> - <?= $reserva['fecha'] ?></h3>

                <div class="reserva-contenido">
                    <!-- Columna izquierda: Información del predio -->
                    <div class="columna columna-predio">
                        <p><strong>Predio:</strong> <?= $reserva['predio']['nombre'] ?></p>
                        <p><strong>📍 Dirección:</strong> <?= $reserva['predio']['direccion'] ?></p>
                        <p><strong>📞 Contacto:</strong> <?= $reserva['predio']['contacto'] ?></p>
                    </div>

                    <!-- Columna central: Imagen -->
                    <div class="columna columna-imagen">
                        <img src="../propietario/<?= $reserva['cancha']['imagen'] ?>" alt="Cancha reservada" class="imagen-reserva">
                    </div>

                    <!-- Columna derecha: Información de la cancha -->
                    <div class="columna columna-cancha">
                        <p><strong>Cancha Nº:</strong> <?= $reserva['cancha']['num_cancha'] ?></p>
                        <p><strong>Tipo:</strong> <?= $reserva['cancha']['tipo'] ?></p>
                        <p><strong>Precio por hora:</strong> $<?= number_format($reserva['cancha']['precio'], 2) ?></p>
                        <p><strong>🕒 Horarios reservados:</strong></p>
                        <ul>
                            <?php foreach ($reserva['horarios'] as $h): ?>
                                <li><?= $h ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No tenés reservas registradas aún.</p>
    <?php endif; ?>
</main>
</body>
</html>
