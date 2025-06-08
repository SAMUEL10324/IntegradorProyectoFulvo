<?php
    session_start();
    include 'conexion_be.php';

    $correo_usuario_login = $_POST['correo'];
    $contrasena_usuario_login = $_POST['contrasena'];
    $rol_usuario_login = $_POST['rol'];

    // Contraseña encriptada
    $contrasena_usuario_login = hash('sha512', $contrasena_usuario_login);

    // Validar si el usuario existe
    $validar_login = mysqli_query($conexion, "SELECT * FROM usuario 
                                                WHERE correo_electronico='$correo_usuario_login' 
                                                AND contraseña='$contrasena_usuario_login' AND rol='$rol_usuario_login'");

    if (mysqli_num_rows($validar_login) > 0) {
        $row = mysqli_fetch_array($validar_login);

        $_SESSION['correo'] = $correo_usuario_login;
        $_SESSION['rol'] = $rol_usuario_login;
        $_SESSION['id_usuario'] = $row['id_usuario']; 

        if ($rol_usuario_login == "propietario") {
            header("Location: ../propietario_main.php");
            exit();
        } else if ($rol_usuario_login == "cliente") {
            header("Location: ../cliente_main.php");
            exit();
        }
    } else {
        echo '
            <script>
                alert("Usuario no existe, por favor verifique los datos ingresados.");
                window.location = "../login_registro.php"
            </script>
        ';
        exit();
    }
?>
