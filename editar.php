<?php
require_once 'auth.php';
require_once 'config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit();
}


// Obtener datos del árbol
$sql = "SELECT * FROM arboles WHERE id = $id";

$result = $conn->query($sql);

$arbol = $result->fetch_assoc();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $especie = $_POST['especie'];
    $ubicacion = $_POST['ubicacion'];
    $fecha = $_POST['fecha_plantacion'];
    $estado = $_POST['estado'];
    $usuario = $_POST['usuario'];

    // Mantener la imagen actual
    $ruta = $arbol['imagen'];

    // Comprobar si se ha subido una imagen nueva
    if ($_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {

        $imagen = $_FILES['imagen'];

        $ruta = "uploads/" . $imagen['name'];

        $rutaFisica = __DIR__ . "/" . $ruta;

        move_uploaded_file(
            $imagen['tmp_name'],
            $rutaFisica
        );
    }


    // Datos que enviaremos a la API
    $datos = [
        'especie' => $especie,
        'ubicacion' => $ubicacion,
        'fecha_plantacion' => $fecha,
        'estado' => $estado,
        'usuario_registro' => $usuario,
        'imagen' => $ruta
    ];


    $json = json_encode($datos);


    // URL del endpoint PUT
    $url = 'https://arboles.paiporta.local/api/arboles.php?id=' . $id;


    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    // Aceptar certificado autofirmado
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);


    $respuesta = curl_exec($ch);


    if ($respuesta === false) {

        die('Error cURL: ' . curl_error($ch));
    }


    $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);


    // Comprobar respuesta de la API
    if ($codigo === 200) {

        registerAction("Tree Updated: ID $id", $usuario);

        header("Location: index.php");

        exit();

    } else {

        echo "Error al actualizar el árbol. Código HTTP: " . $codigo;

        echo "<pre>";
        echo htmlspecialchars($respuesta);
        echo "</pre>";
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>PaiportArbolado : Editar Árbol</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="form-container">

        <h1>Editar Árbol</h1>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?= $arbol['id'] ?>">

            <div class="campo">
                <label>Especie:</label>

                <input
                    type="text"
                    name="especie"
                    value="<?= htmlspecialchars($arbol['especie']) ?>"
                    required
                >
            </div>

            <div class="campo">
                <label>Ubicación:</label>

                <input
                    type="text"
                    name="ubicacion"
                    value="<?= htmlspecialchars($arbol['ubicacion']) ?>"
                    required
                >
            </div>

            <div class="campo">
                <label>Fecha de Plantación:</label>

                <input
                    type="date"
                    name="fecha_plantacion"
                    value="<?= $arbol['fecha_plantacion'] ?>"
                    required
                >
            </div>

            <div class="campo">
                <label>Estado:</label>

                <select name="estado" required>

                    <option
                        value="sano"
                        <?= $arbol['estado'] === 'sano' ? 'selected' : '' ?>
                    >
                        Sano
                    </option>

                    <option
                        value="enfermo"
                        <?= $arbol['estado'] === 'enfermo' ? 'selected' : '' ?>
                    >
                        Enfermo
                    </option>

                    <option
                        value="talado"
                        <?= $arbol['estado'] === 'talado' ? 'selected' : '' ?>
                    >
                        Talado
                    </option>

                </select>
            </div>

            <div class="campo">
                <label>Usuario:</label>

                <input
                    type="text"
                    name="usuario"
                    value="<?= htmlspecialchars($arbol['usuario_registro']) ?>"
                    required
                >
            </div>

            <div class="campo">
                <label>Imagen actual:</label>

                <?php if (!empty($arbol['imagen'])): ?>

                    <br>

                    <img
                        src="<?= $arbol['imagen'] ?>"
                        width="150"
                    >

                <?php else: ?>

                    <p>Este árbol no tiene imagen.</p>

                <?php endif; ?>
            </div>

            <div class="campo">
                <label>Nueva imagen:</label>

                <input
                    type="file"
                    name="imagen"
                >
            </div>

            <button type="submit">
                Actualizar
            </button>

        </form>

        <a href="index.php">
            Volver a la lista
        </a>

    </div>

</body>

</html>
