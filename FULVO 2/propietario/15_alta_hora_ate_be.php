<?php
include '../login/conexion_be.php';

if (!isset($_POST['id_predio']) || empty($_POST['dias'])) {
    echo 
        '<script>
            alert("Datos incompletos."); 
            window.location = "15_alta_hora_ate.php";
        </script>';
    exit();
}

$id_predio = intval($_POST['id_predio']);
$dias = $_POST['dias'];
$aperturas = $_POST['aperturas'];
$cierres = $_POST['cierres'];

// Validación: ¿hay días repetidos en la selección del formulario?
if (count($dias) !== count(array_unique($dias))) {
    echo 
        '<script>
            alert("No se pueden seleccionar días repetidos."); 
            window.location = "15_alta_hora_ate.php";
        </script>';
    exit();
}

for ($i = 0; $i < count($dias); $i++) {
    $dia = intval($dias[$i]);
    $hora_apertura = $aperturas[$i];
    $hora_cierre = $cierres[$i];

    // Verificar si ya existe ese día para ese predio
    $verificar = mysqli_query($conexion, "
        SELECT * FROM Horario_Atencion
        WHERE Predio_id_predio = $id_predio AND Dias_id_dias = $dia
    ");

    if (mysqli_num_rows($verificar) > 0) {
        // Reemplazar horario existente (UPDATE)
        $query = "UPDATE Horario_Atencion 
                  SET horario_apertura = '$hora_apertura', horario_cierre = '$hora_cierre' 
                  WHERE Predio_id_predio = $id_predio AND Dias_id_dias = $dia";
    } else {
        // Insertar nuevo (INSERT)
        $query = "INSERT INTO Horario_Atencion 
                  (horario_apertura, horario_cierre, Predio_id_predio, Dias_id_dias)
                  VALUES ('$hora_apertura', '$hora_cierre', $id_predio, $dia)";
    }

    mysqli_query($conexion, $query);
}

echo 
    '<script>
        alert("Horarios guardados correctamente."); 
        window.location = "14_gestion_horarios.php";
    </script>';
?>


