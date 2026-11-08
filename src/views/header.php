<?php
require_once __DIR__ . '/../services/SessionService.php';
SessionService::init();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tienda Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <a href="index.php" class="text-decoration-none h3 text-dark m-0">Tienda Online</a>
        <div>
            <?php if (SessionService::isLoggedIn()): ?>
                <span class="me-3">Hola, <strong><?php echo htmlspecialchars(SessionService::get('username')); ?></strong></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar Sesión</a>
                <?php if (SessionService::get('role') === 'admin'): ?>
                    <span class="badge bg-success ms-1">Admin</span>
                    <a href="create.php" class="btn btn-success btn-sm ms-1">＋ Producto</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-sm">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </div>