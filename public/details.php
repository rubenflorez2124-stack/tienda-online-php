<?php
require_once __DIR__ . '/../src/services/SessionService.php';
require_once __DIR__ . '/../src/services/ProductService.php';

SessionService::init();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$product = ProductService::getProductById($id);
if (!$product) {
    header('Location: index.php');
    exit;
}

// Obtener las categorías para mostrar el nombre de la categoría actual si es necesario
$categorias = ProductService::getCategories();
$nombreCategoria = "Sin categoría";
foreach ($categorias as $cat) {
    if ($cat['id'] === $product['categoria_id']) {
        $nombreCategoria = $cat['nombre'];
        break;
    }
}

$isAdmin = SessionService::isLoggedIn() && SessionService::get('role') === 'admin';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['marca'] . ' ' . $product['modelo']); ?> - Detalles</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container py-5">
    <!-- Cabecera de web -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php" class="btn btn-outline-secondary btn-sm">← Volver al catálogo</a>
        <div>
            <?php if (SessionService::isLoggedIn()): ?>
                <span class="me-2">Hola, <strong><?php echo htmlspecialchars(SessionService::get('username')); ?></strong></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-sm">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </div>

    <hr class="mb-5">

    <!-- Detalle del Producto -->
    <div class="row g-5 align-items-center">

        <div class="col-md-6 text-center">
            <div class="card shadow-sm p-3 border-0">
                <img src="<?php echo htmlspecialchars($product['imagen'] ?: 'https://via.placeholder.com/400'); ?>"
                    class="img-fluid rounded mx-auto"
                    alt="Imagen de <?php echo htmlspecialchars($product['modelo']); ?>"
                    style="max-height: 400px; object-fit: cover;">
            </div>
        </div>

        <!-- Información -->
        <div class="col-md-6">
            <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($nombreCategoria); ?></span>
            <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($product['marca'] . ' ' . $product['modelo']); ?></h1>

            <div class="mb-3">
                <span class="fs-3 fw-bold text-primary">$<?php echo number_format($product['precio'], 2); ?></span>
                <span class="badge bg-secondary ms-2 fs-6">Stock disponible: <?php echo $product['stock']; ?> unidades</span>
            </div>

            <p class="lead text-muted mb-4"><?php echo nl2br(htmlspecialchars($product['descripcion'])); ?></p>

            <div class="d-grid gap-2">
                <button class="btn btn-dark btn-lg" disabled>Añadir al Carrito (Próximamente)</button>
            </div>

            <?php if ($isAdmin): ?>
                <div class="mt-4 p-3 bg-light rounded border">
                    <h6 class="text-muted mb-2">Panel de Administración Rápido</h6>
                    <div class="btn-group w-100" role="group">
                        <a href="update.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Editar Datos</a>
                        <a href="update-image.php?id=<?php echo $product['id']; ?>" class="btn btn-info btn-sm text-white">Cambiar Imagen</a>
                        <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este producto?');">Eliminar</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
