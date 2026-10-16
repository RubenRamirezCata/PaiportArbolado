<?php
require_once 'auth.php';
// require_once 'config.php';

// Consultar árboles
// $sql = "SELECT * FROM arboles";
// $result = $conn->query($sql);

$url = 'https://arboles.paiporta.local/api/arboles.php';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$respuesta = curl_exec($ch);

if ($respuesta == false) {
    die('Error al conectar con la API: ' . curl_error($ch));
}

curl_close($ch);

// Convertir el JSON recibido en un array PHP
$arboles = json_decode($respuesta, true);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title> PaiportArbolado : Árboles de Paiporta</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Gestión de Árboles de Paiporta</h1>
    <a href="crear.php"> Añadir nuevo árbol</a>
    <input type="text" id="buscar" placeholder="Buscar por especie o ubicación..." onkeyup="searchTrees()">
    <div class="logout">
	Bienvenido <?= $_SESSION['nombre']?> |
	<a href="logout.php">
	    Cerrar sesión
	    <img src="cerrarSesion.png" alt="Cerrar sesión">
        </a>
    </div>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Especie</th>
            <th>Ubicación</th>
            <th>Fecha Plantación</th>
            <th>Estado</th>
	    <th>Usuario</th>
	    <th>Imagen</th>
            <th>Acciones</th>
        </tr>
	    <?php foreach ($arboles as $row): ?>
            <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['especie']) ?></td>
            <td><?= htmlspecialchars($row['ubicacion']) ?></td>
            <td><?= $row['fecha_plantacion'] ?></td>
            <td><?= $row['estado'] ?></td>
	    <td><?= $row['usuario_registro'] ?></td>
	    <td>
            <?php if (!empty($row['imagen'])): ?>
                <img src="<?= htmlspecialchars($row['imagen']) ?>" width="100">
            <?php else: ?>
                Sin imagen
            <?php endif; ?>
        </td>
            <td>
            <a href="editar.php?id=<?= $row['id'] ?>">Editar</a>
            <a href="eliminar.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este árbol?')">Eliminar</a>
            </td>
            </tr>
	    <?php endforeach; ?>
    </table>
    <div class="dashboard-link">
	<a href="dashboard.php">
	    <button type="button">Mostrar dashboards</button>
	</a>
    </div>
    <script src="js/script.js"></script>
    </body>
</html>
<?php $conn->close(); ?>
