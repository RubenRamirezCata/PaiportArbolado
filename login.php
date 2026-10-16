<?php
session_start();

require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['usuario'] = $user['username'];
        $_SESSION['nombre'] = $user['nombre'];

        header('Location: index.php');
        exit;

    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Iniciar sesión - PaiportArbolado</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <div class="login-container">

        <h1>PaiportArbolado</h1>

        <?php if ($error): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="campo">

                <label for="username">
                    Usuario
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                >

            </div>

            <div class="campo">

                <label for="password">
                    Contraseña
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >

            </div>

            <button type="submit">
                Iniciar sesión
            </button>

        </form>

    </div>

</body>

</html>
