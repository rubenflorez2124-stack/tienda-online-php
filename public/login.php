<?php
// public/login.php
require_once __DIR__ . '/../src/services/SessionService.php';
require_once __DIR__ . '/../src/config/Config.php';

SessionService::init();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $db = Config::getConnection();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = ? AND is_deleted = false");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $passwordMatch = false;

            // Soporta contraseña en texto plano (como 'admin') o hasheada
            if ($password === $user['password']) {
                $passwordMatch = true;
            } elseif (password_verify($password, $user['password'])) {
                $passwordMatch = true;
            }

            if ($passwordMatch) {
                SessionService::set('user_id', $user['id']);
                SessionService::set('username', $user['username']);
                SessionService::set('nombre', $user['nombre']);

                // Comprobación de rol administrador
                $isAdmin = ($user['username'] === 'admin');
                SessionService::set('role', $isAdmin ? 'admin' : 'USER');

                header('Location: index.php');
                exit;
            } else {
                $error = 'Contraseña incorrecta.';
            }
        } else {
            $error = 'El usuario no existe en la base de datos.';
        }
    } catch (Exception $e) {
        $error = 'Error de conexión con la base de datos: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Tienda Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container py-5" style="max-width: 500px;">
    <h2 class="mb-4">Iniciar Sesión</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>

    <div class="mt-3 text-center">
        <a href="index.php" class="text-decoration-none">← Volver al inicio</a>
    </div>
</body>

</html>