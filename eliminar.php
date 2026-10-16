<?php

require_once 'auth.php';
require_once 'config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit();
}


$usuario = $_SESSION['nombre'];


// URL del endpoint DELETE
$url = 'https://arboles.paiporta.local/api/arboles.php?id=' . $id;


// Crear conexión cURL
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');


curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);


$respuesta = curl_exec($ch);


if ($respuesta === false) {

    die('Error cURL: ' . curl_error($ch));
}


// Obtener código HTTP
$codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);


if ($codigo === 200) {

    registerAction("Tree Deleted: ID $id", $usuario);

    header("Location: index.php");

    exit();

} else {

    echo "Error al eliminar el árbol. Código HTTP: " . $codigo;

    echo "<pre>";
    echo htmlspecialchars($respuesta);
    echo "</pre>";
}

?>
