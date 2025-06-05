<?php

    include 'conexion_be.php';

    $nombre_usuario =  $_POST['nombre'];
    $apellido_usuario = $_POST['apellido'];
    $dni_usuario = $_POST['dni'];
    $correo_usuario = $_POST['correo'];
    $contrasena_usuario = $_POST['contrasena'];
    $rol_usuario = $_POST['rol'];

    //Encriptamiento de contraseña
    $contrasena_encriptada = hash('sha512', $contrasena_usuario);

    //Verificar que el correo, dni no se repitan en la base de datos
    $verificar_correo = mysqli_query($conexion, "SELECT * FROM usuario WHERE correo_electronico='$correo_usuario' ");
    if(mysqli_num_rows($verificar_correo) > 0){
        echo'
            <script>
                alert("Este correo ya está en uso, intenta con otro diferente.");
                window.location = "../login_registro.php"
            </script>
        ';
        exit();
    }
    $verificar_dni = mysqli_query($conexion, "SELECT * FROM usuario WHERE dni='$dni_usuario'");
    if(mysqli_num_rows($verificar_dni) > 0){
        echo'
            <script>
                alert("Este DNI ya existe.");
                window.location = "../login_registro.php"
            </script>
        ';
        exit();
    }

    $insertar_usuario = "INSERT INTO usuario(nombre, apellido, dni, correo_electronico, contraseña, rol) 
            VALUES('$nombre_usuario', '$apellido_usuario', '$dni_usuario', '$correo_usuario', '$contrasena_encriptada', '$rol_usuario')";


    $ejecutar = mysqli_query($conexion, $insertar_usuario);

    if($ejecutar){
        $id_usuario = mysqli_insert_id($conexion);

        if($rol_usuario === 'cliente'){
            $insertar_cliente = "INSERT INTO cliente(Usuario_id_usuario)
            VALUES ('$id_usuario')";
            mysqli_query($conexion, $insertar_cliente);
        }elseif($rol_usuario === 'propietario'){
            $insertar_propietario = "INSERT INTO propietario(Usuario_id_usuario)
            VALUES('$id_usuario')";
            mysqli_query($conexion, $insertar_propietario);
        }

        echo'
            <script>
                alert("Usuario registrado.");
                window.location = "../login_registro.php";
            </script>
        ';
    }else{
            echo'
                <script>
                    alert("Error, intente de nuevo.");
                    window.location = "../login_registro.php";
                </script>
            ';
    }
    
    mysqli_close($conexion);
?>