<?php

    //mantener la sesion abierta
    session_start();

    if (isset($_SESSION['correo']) && isset($_SESSION['rol'])) {
        if ($_SESSION['rol'] == 'propietario') {
            header("Location: propietario_main.php");
            exit();
        } elseif ($_SESSION['rol'] == 'cliente') {
            header("Location: cliente_main.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro - Fulvo </title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wdth,wght@0,75..100,100..900;1,75..100,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <main>
        <div class="contenedor__todo">

            <div class="caja__trasera">
                <div class="caja__trasera-login">
                    <h3>¿Ya tenes una cuenta?</h3>
                    <p>Inicia sesión para entrar</p>
                    <button id="btn__iniciar-sesion">Iniciar Sesión</button>
                </div>
                <div class="caja__trasera-registro">
                    <h3>¿No tenes cuenta?</h3>
                    <p>Registrate para inciar sesión</p>
                    <button id="btn__registrarse">Registrarse</button>
                </div>
            </div>
            
            <!--Formulario para el login y registro-->
            <div class="contenedor__login-registro">
                <!--Login-->
                <form action="login/login_usuario_be.php" method="POST" class="formulario_login">
                    <h2>Iniciar Sesión</h2>
                    <input type="text" placeholder="Correo Electronico" required name="correo">
                    <input type="password" placeholder="Contraseña" required name="contrasena">
                    <select id="rol" name="rol" required name="rol">
                        <option value="" disabled selected>Selecciona tu rol</option>
                        <option value="cliente">Cliente</option>
                        <option value="propietario">Propietario</option>
                    </select>
                    <button>Entrar</button>
                </form>
                <!--Registro-->
                <form action="login/registro_usuario_be.php" method="POST" class="formulario_registro">
                    <h2>Registrarse</h2>
                    <input type="text" placeholder="Nombre" name="nombre">
                    <input type="text" placeholder="Apellido" name="apellido">
                    <input type="text" placeholder="Correo Electrónico" name="correo">
                    <input type="password" placeholder="Contraseña" name="contrasena">
                    <input type="number" placeholder="DNI" name="dni">
                    <select id="rol_registro" name="rol" required>
                        <option value="" disabled selected>Selecciona tu rol</option>
                        <option value="cliente">Cliente</option>
                        <option value="propietario">Propietario</option>
                    </select>
                    <button>Registrarse</button>
                </form>
            </div>
        </div>

    </main>
    <script src="assets/js/script.js"></script>

</body>
</html>