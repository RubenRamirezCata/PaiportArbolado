<?php
require_once 'auth.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $especie = $conn->real_escape_string($_POST['especie']);
    $ubicacion = $conn->real_escape_string($_POST['ubicacion']);
    $fecha = $_POST['fecha_plantacion'];
    $usuario = $_SESSION['nombre'];
    $imagen = $_FILES['imagen'];

    $ruta = "uploads/" . $imagen['name'];
    $rutaFisica = __DIR__ . "/" . $ruta;
    move_uploaded_file($imagen['tmp_name'], $rutaFisica);

    $datos = [
	'especie' => $especie,
	'ubicacion' => $ubicacion,
	'fecha_plantacion' => $fecha,
	'usuario_registro' => $usuario,
	'imagen' => $ruta
    ];

    $json = json_encode($datos);

    $url = 'https://arboles.paiporta.local/api/arboles.php';

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
	'Content-Type: application/json'
    ]);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $respuesta = curl_exec($ch);

    if($respuesta === false) {
	die('Error cURL: '. curl_error($ch));
    }

    curl_close($ch);
    registerAction("Tree added $especie in $ubicacion", $usuario);
    header("Location: index.php");

    exit();

    /*$sql = "INSERT INTO arboles (especie, ubicacion, fecha_plantacion, usuario_registro, imagen)
    VALUES ('$especie', '$ubicacion', '$fecha', '$usuario', '$ruta')";

    if ($conn->query($sql)) {
        registerAction("Tree added $especie in $ubicacion", $usuario);
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
    */
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>PaiportArbolado: Añadir Árbol</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="form-container">

        <h1>Añadir Nuevo Árbol</h1>

        <form method="POST" enctype="multipart/form-data">

            <div class="campo">
                <label>Especie:</label>
                <input type="text" name="especie" required>
            </div>

            <div class="campo">
                <label>Ubicación:</label>
                <input type="text" name="ubicacion" required>
            </div>

            <div class="campo">
                <label>Fecha de Plantación:</label>
                <input type="date" name="fecha_plantacion" required>
            </div>

            <div class="campo">
                <label>Usuario:</label>
                <input type="text" name="usuario" value="<?= htmlspecialchars($_SESSION['nombre']) ?>" disabled>
            </div>

            <div class="campo">
                <label>Imagen:</label>
                <input type="file" name="imagen" required>
            </div>

            <button type="submit">Guardar</button>

        </form>

        <a href="index.php">Volver a la lista</a>

    </div>

</body>

</html>
