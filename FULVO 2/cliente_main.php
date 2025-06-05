<?PHP

    session_start();

    if(!isset($_SESSION['correo']) || $_SESSION['rol'] != 'cliente'){
        echo'
            <script>
                alert("Debes inciar sesion.");
                window.location = "login_registro.php";
            </script>
        ';
        session_destroy();
        die();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLIENTE</title>
</head>
<body>
    <h1>CLIENTE</h1>
    <a href="login/cerrar_sesion.php">Cerrar sesión</a>
</body>
</html>