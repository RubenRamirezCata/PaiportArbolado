
<?php

require_once 'auth.php';
require_once 'config.php';

// Consulta para contar los árboles por especie
$sql = "SELECT especie, COUNT(*) AS total
        FROM arboles
        GROUP BY especie
        ORDER BY total DESC";

$result = $conn->query($sql);

// Arrays para enviar los datos a JavaScript
$especies = [];
$totales = [];

while ($row = $result->fetch_assoc()) {
    $especies[] = $row['especie'];
    $totales[] = $row['total'];
}


// Consulta para contar los árboles por estado
$sqlEstado = "SELECT estado, COUNT(*) AS total
              FROM arboles
              GROUP BY estado
              ORDER BY total DESC";

$resultEstado = $conn->query($sqlEstado);

$estados = [];
$totalesEstado = [];

while ($row = $resultEstado->fetch_assoc()) {
    $estados[] = $row['estado'];
    $totalesEstado[] = $row['total'];
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Dashboard - PaiportArbolado</title>

    <link rel="stylesheet" href="css/style.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>

        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .dashboard-graficos {
            display: flex;
            gap: 30px;
            width: 100%;
        }

        .grafico {
            flex: 1;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-sizing: border-box;
        }

        .grafico h2 {
            text-align: center;
            margin-top: 0;
        }

        .grafico canvas {
            width: 100% !important;
            height: 500px !important;
        }

    </style>

</head>

<body>

    <h1>Dashboard PaiportArbolado</h1>

    <div class="dashboard-graficos">

        <!-- Gráfico de árboles por especie -->
        <div class="grafico">

            <h2>Árboles por especie</h2>

            <canvas id="arbolesPorEspecie"></canvas>

            <script>

                // Datos procedentes de PHP
                const labels = <?= json_encode($especies) ?>;
                const datos = <?= json_encode($totales) ?>;

                // Obtenemos el canvas
                const ctx = document.getElementById('arbolesPorEspecie');

                // Creamos el gráfico
                new Chart(ctx, {

                    type: 'bar',

                    data: {

                        labels: labels,

                        datasets: [{
                            label: 'Número de árboles',
                            data: datos
                        }]

                    },

                    options: {
                        responsive: true
                    }

                });

            </script>

        </div>


        <!-- Gráfico de árboles por estado -->
        <div class="grafico">

            <h2>Árboles por estado</h2>

            <canvas id="arbolesPorEstado"></canvas>

            <script>

                const estados = <?= json_encode($estados) ?>;
                const datosEstado = <?= json_encode($totalesEstado) ?>;

                const ctxEstado = document.getElementById('arbolesPorEstado');

                new Chart(ctxEstado, {

                    type: 'pie',

                    data: {

                        labels: estados,

                        datasets: [{
                            label: 'Número de árboles',
                            data: datosEstado
                        }]

                    },

                    options: {
                        responsive: true
                    }

                });

            </script>

        </div>

    </div>

    <div class="dashboard-link">
        <a href="index.php">
            <button type="button">Volver al inicio</button>
        </a>
    </div>

</body>

</html>
