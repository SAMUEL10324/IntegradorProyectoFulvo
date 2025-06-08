<?php
    session_start();
    $id_usuario = $_SESSION['id_usuario'];

    if(!isset($_SESSION['correo']) || $_SESSION['rol'] != 'propietario'){
        echo '
            <script>
                alert("Acceso denegado. Inicia sesión.");
                window.location = "login_registro.php";
            </script>
        ';
        session_destroy();
        die();
    }

    include '../login/conexion_be.php';

    //DATOS DEL FORMULARIO
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $contacto = $_POST['contacto'];

    $calle = $_POST['calle'];
    $numero = $_POST['numero'];
    $ciudad = $_POST['ciudad'];
    $provincia = $_POST['provincia'];

    $hora_aper = $_POST['hora_aper'];
    $hora_cierre = $_POST['hora_cierre'];

    //IMAGEN
    $foto_predio = $_FILES['foto_predio']['name'];
    $ruta_temporal = $_FILES['foto_predio']['tmp_name'];
    $carpeta_destino = 'imagenes/'; // Asegúrate que exista y tenga permisos
    $nombre_imagen = uniqid() . '_' . $foto_predio;
    $ruta_final = $carpeta_destino . $nombre_imagen;

    //SUBIR IMAGEN
    if(!move_uploaded_file($ruta_temporal, $ruta_final)){
        echo '
            <script>
                alert("Error al subir la imagen.");
                window.location = "1_alta_predio.php";
            </script>
        ';
        exit();
    }

    //OBTENER ID DEL PROPIETARIO
    $query_propietario = "SELECT id_propietario FROM Propietario WHERE Usuario_id_usuario = '$id_usuario'";
    $result_prop = mysqli_query($conexion, $query_propietario);
    if(mysqli_num_rows($result_prop) == 0){
        echo '
            <script>
                alert("Propietario no encontrado.");
                window.location = "1_alta_predio.php";
            </script>
        ';
        exit();
    }
    $fila_prop = mysqli_fetch_assoc($result_prop);
    $id_propietario = $fila_prop['id_propietario'];

    //INSERTAR UBICACIÓN
    $query_ubicacion = "INSERT INTO Ubicacion (calle, numero, ciudad, provincia) 
                        VALUES ('$calle', '$numero', '$ciudad', '$provincia')";

    if(!mysqli_query($conexion, $query_ubicacion)){
        echo '
            <script>
                alert("Error al registrar la ubicación.");
                window.location = "1_alta_predio.php";
            </script>
        ';
        exit();
    }

    //OBTENER ID UBICACIÓN RECIÉN CREADA
    $id_ubicacion = mysqli_insert_id($conexion);

    //INSERTAR PREDIO
    $query_predio = "INSERT INTO Predio 
        (nombre, descripcion, contacto, foto_predio, Ubicacion_id_ubicacion, Propietario_id_propietario)
        VALUES 
        ('$nombre', '$descripcion', '$contacto', '$ruta_final', '$id_ubicacion', '$id_propietario')";

    if(mysqli_query($conexion, $query_predio)){
        echo '
            <script>
                alert("Predio registrado exitosamente.");
                window.location = "1_alta_predio.php";
            </script>
        ';
    } else {
        echo '
            <script>
                alert("Error al registrar el predio.");
                window.location = "1_alta_predio.php";
            </script>
        ';
    }

    mysqli_close($conexion);
?>
