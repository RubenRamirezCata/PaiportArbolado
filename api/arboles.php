<?php

require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');


// GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['buscar']) && $_GET['buscar'] !== '') {

        $buscar = $_GET['buscar'];

        $sql = "SELECT * FROM arboles
                WHERE especie LIKE ?
                   OR ubicacion LIKE ?
                   OR fecha_plantacion LIKE ?
                   OR estado LIKE ?
                   OR usuario_registro LIKE ?";

        $stmt = $conn->prepare($sql);

        $busqueda = '%' . $buscar . '%';

        $stmt->bind_param(
            "sssss",
            $busqueda,
            $busqueda,
            $busqueda,
            $busqueda,
            $busqueda
        );

        $stmt->execute();

        $result = $stmt->get_result();

    } else {

        $sql = "SELECT * FROM arboles";

        $result = $conn->query($sql);
    }

    $arboles = [];

    while ($row = $result->fetch_assoc()) {
        $arboles[] = $row;
    }

    echo json_encode($arboles);
}


// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datos = json_decode(file_get_contents('php://input'), true);

    $especie = $datos['especie'];
    $ubicacion = $datos['ubicacion'];
    $fecha_plantacion = $datos['fecha_plantacion'];
    $usuario_registro = $datos['usuario_registro'];
    $imagen = $datos['imagen'];

    $sql = "INSERT INTO arboles
            (especie, ubicacion, fecha_plantacion, usuario_registro, imagen)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssss",
        $especie,
        $ubicacion,
        $fecha_plantacion,
        $usuario_registro,
        $imagen
    );

    if ($stmt->execute()) {

        echo json_encode([
            "mensaje" => "Árbol creado correctamente",
            "id" => $conn->insert_id
        ]);

    } else {

        echo json_encode([
            "error" => "No se ha podido crear el árbol"
        ]);
    }

    $stmt->close();
}


// PUT
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    if (!isset($_GET['id'])) {

        echo json_encode([
            "error" => "Falta el ID del árbol"
        ]);

        exit;
    }

    $id = $_GET['id'];

    $datos = json_decode(file_get_contents('php://input'), true);

    $especie = $datos['especie'];
    $ubicacion = $datos['ubicacion'];
    $fecha_plantacion = $datos['fecha_plantacion'];
    $estado = $datos['estado'];
    $usuario_registro = $datos['usuario_registro'];
    $imagen = $datos['imagen'];

    $sql = "UPDATE arboles
            SET especie = ?,
                ubicacion = ?,
                fecha_plantacion = ?,
                estado = ?,
                usuario_registro = ?,
                imagen = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssssi",
        $especie,
        $ubicacion,
        $fecha_plantacion,
        $estado,
        $usuario_registro,
        $imagen,
        $id
    );

    if ($stmt->execute()) {

        echo json_encode([
            "mensaje" => "Árbol actualizado correctamente"
        ]);

    } else {

        echo json_encode([
            "error" => "No se ha podido actualizar el árbol"
        ]);
    }

    $stmt->close();
}


// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    if (!isset($_GET['id'])) {

        echo json_encode([
            "error" => "Falta el ID del árbol"
        ]);

        exit;
    }

    $id = $_GET['id'];

    $sql = "DELETE FROM arboles WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        echo json_encode([
            "mensaje" => "Árbol eliminado correctamente"
        ]);

    } else {

        echo json_encode([
            "error" => "No se ha podido eliminar el árbol"
        ]);
    }

    $stmt->close();
}


$conn->close();

?>
