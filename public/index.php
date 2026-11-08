<?php
// public/index.php
require_once __DIR__ . '/../src/services/SessionService.php';
require_once __DIR__ . '/../src/services/ProductService.php';
require_once __DIR__ . '/../src/services/CategoriasService.php';

SessionService::init();

// Obtener filtros de búsqueda o categoría si existen
$busqueda = $_GET['busqueda'] ?? '';
$categoria_id = $_GET['categoria_id'] ?? '';

// Obtener productos y categorías usando los servicios
$productos = ProductService::getAllProducts($busqueda, $categoria_id);
$categorias = CategoriasService::getAllCategories();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Catálogo - Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Tienda Online</a>
            <div class="d-flex align-items-center">
                <?php if (SessionService::isLoggedIn()): ?>
                    <span class="text-white me-3">Hola, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Usuario') ?></strong></span>
                    <?php if (SessionService::isAdmin()): ?>
                        <span class="badge bg-success me-2">Admin</span>
                        <a href="create.php" class="btn btn-sm btn-outline-light me-2">+ Añadir Producto</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-sm btn-danger">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-outline-light me-2">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4">Catálogo de Productos</h1>

        <!-- Formulario de Búsqueda y Filtros -->
        <form method="GET" action="index.php" class="row g-3 mb-4">
            <div class="col-md-8">
                <input type="text" class="form-control" name="busqueda" placeholder="Buscar por marca, modelo o descripción..." value="<?= htmlspecialchars($busqueda) ?>">
            </div>
            <div class="col-md-3">
                <select name="categoria_id" class="form-select">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoria_id == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>

        <!-- Cuadrícula de Productos -->
        <div class="row">
            <?php if (empty($productos)): ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">No se encontraron productos disponibles.</div>
                </div>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">

                            <!-- RUTA DE LA IMAGEN: Apunta a public/uploads/ -->
                            <?php if (!empty($producto['imagen'])): ?>
                                <img src="uploads/<?= htmlspecialchars($producto['imagen']) ?>" class="card-img-top" alt="Imagen del producto" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/150x150?text=Sin+Imagen" class="card-img-top" alt="Sin imagen" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($producto['nombre'] ?? '') ?></h5>
                                <p class="card-text text-muted"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></p>
                                <div class="mt-auto">
                                    <h6 class="text-primary fw-bold">$<?= number_format($producto['precio'] ?? 0, 2) ?></h6>

                                    <div class="d-grid gap-2 mt-3">
                                        <a href="details.php?id=<?= $producto['id'] ?>" class="btn btn-outline-primary btn-sm">Detalles</a>

                                        <?php if (SessionService::isAdmin()): ?>
                                            <div class="btn-group" role="group">
                                                <a href="update.php?id=<?= $producto['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                                <!-- ENLACE ACTUALIZADO A update-image.php -->
                                                <a href="update-image.php?id=<?= $producto['id'] ?>" class="btn btn-info btn-sm text-white">Imagen</a>
                                                <a href="delete.php?id=<?= $producto['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este producto?');">Eliminar</a>
                                            </div>
                                        <?php endif; ?>

                                        <a href="#" class="btn btn-secondary btn-sm">Comprar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>