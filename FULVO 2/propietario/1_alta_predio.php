<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wdth,wght@0,75..100,100..900;1,75..100,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilosPropietario.css">
    <title>Alta Predio</title>
</head>
<body>
    <header class="barra_superior">
        <h1>FULVO.COM</h1>
        <a href="11_gestion_predio.php" class="btn_volver">Volver</a>

    </header>
    <main>
        <div class="form_alta_predio">
            <h3>Alta Predio</h3>
            <form action="1_alta_predio_be.php" method="post" enctype="multipart/form-data">
                <input type="text" name="nombre" placeholder="Nombre del predio" required><br>
                <input type="text" name="descripcion" placeholder="Descripcion" required><br>
                <input type="text" name="contacto" placeholder="Numero o Mail de contacto" required><br>
                <body class="fondo_predio">


                <!-- Input de imagen -->
                <input type="file" name="foto_predio" accept="image/*" onchange="mostrarVistaPrevia(this)" required><br>
                
                <!-- Imagen para la vista previa -->
                <img id="vista-previa" src="#" alt="Vista previa" style="display:none; max-width: 250px; margin-top: 10px;"><br>

                <!-- Ubicación -->
                <input type="text" name="calle" placeholder="Calle" required><br>
                <input type="text" name="numero" placeholder="Numero de calle" required><br>
                <input type="text" name="ciudad" placeholder="Ciudad" required><br>
                <input type="text" name="provincia" placeholder="Provincia" required><br>
                
                <button type="submit">Registrar Predio</button>
            </form>
            <h2></h2>
            <h3></h3>
        </div>
    </main>

    <!-- Script para vista previa -->
    <script>
        function mostrarVistaPrevia(input) {
            const archivo = input.files[0];
            const vistaPrevia = document.getElementById('vista-previa');

            if (archivo) {
                const lector = new FileReader();
                lector.onload = function (e) {
                    vistaPrevia.src = e.target.result;
                    vistaPrevia.style.display = "block";
                };
                lector.readAsDataURL(archivo);
            } else {
                vistaPrevia.src = "#";
                vistaPrevia.style.display = "none";
            }
        }
    </script>
</body>
</html>