<?php
session_start();
include '../login/conexion_be.php';

if (!isset($_SESSION['correo']) || $_SESSION['rol'] !== 'cliente') {
    echo '<script>alert("Debes iniciar sesión."); window.location = "../login_registro.php";</script>';
    session_destroy();
    die();
}

$id_usuario = $_SESSION['id_usuario'];
$id_predio = $_POST['id_predio'] ?? null;
$id_cancha = $_POST['id_cancha'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$horarios = $_POST['horarios'] ?? [];

if (!$id_predio || !$id_cancha || !$fecha || empty($horarios)) {
    echo '<script>alert("Faltan datos de la reserva."); window.location = "../cliente_main.php";</script>';
    exit;
}

// 1. Obtener ID del cliente desde Usuario
$query_cliente = mysqli_query($conexion, "SELECT id_cliente FROM Cliente WHERE Usuario_id_usuario = $id_usuario");
$cliente = mysqli_fetch_assoc($query_cliente);
$id_cliente = $cliente['id_cliente'] ?? null;

if (!$id_cliente) {
    echo '<script>alert("Error al identificar al cliente."); window.location = "../cliente_main.php";</script>';
    exit;
}

// 2. Insertar en Reserva
$num_reserva = rand(1, 1000); // Generar número único
$estado = 'pendiente';

$insert_reserva = mysqli_query($conexion, "
    INSERT INTO Reserva (fecha, estado_reserva, num_reserva, Cliente_id_cliente)
    VALUES ('$fecha', '$estado', $num_reserva, $id_cliente)
");

if (!$insert_reserva) {
    echo '<script>alert("Error al insertar la reserva."); window.location = "../cliente_main.php";</script>';
    exit;
}

$id_reserva = mysqli_insert_id($conexion); // Obtener ID autogenerado

// 3. Insertar cada detalle de reserva
foreach ($horarios as $horario) {
    list($hora_inicio, $hora_salida) = explode(' - ', $horario);

    $insert_detalle = mysqli_query($conexion, "
        INSERT INTO Detalle_Reserva (hora_inicio, hora_salida, Reserva_id_reserva, Cancha_id_cancha)
        VALUES ('$hora_inicio', '$hora_salida', $id_reserva, $id_cancha)
    ");

    if (!$insert_detalle) {
        echo '<script>alert("Error al registrar uno de los horarios."); window.location = "../cliente_main.php";</script>';
        exit;
    }
}

// 4. Redirigir o mostrar mensaje de éxito
echo '
    <script>
        alert("¡Reserva realizada con éxito!");
        window.location = "5_gestionar_reservas.php";
    </script>
';
?>
