<?php
// public/update.php
require_once __DIR__ . '/../src/services/SessionService.php';
require_once __DIR__ . '/../src/services/ProductService.php';

SessionService::init();

// Seguridad: Solo administradores
if (!SessionService::isLoggedIn() || SessionService::get('role') !== 'admin') {
    header('Location: index.php');
    exit;
}

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

$categorias = ProductService::getCategories();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoria_id = trim($_POST['categoria_id'] ?? '');

    if (empty($marca) || empty($modelo) || $precio <= 0) {
        $error = 'Por favor, rellena los campos obligatorios (Marca, Modelo y Precio).';
    } else {
        $result = ProductService::updateProduct($id, [
            'marca' => $marca,
            'modelo' => $modelo,
            'precio' => $precio,
            'stock' => $stock,
            'descripcion' => $descripcion,
            'categoria_id' => $categoria_id
        ]);

        if ($result) {
            $success = '¡Producto actualizado con éxito!';
            // Recargar datos actualizados
            $product = ProductService::getProductById($id);
        } else {
            $error = 'Hubo un error al actualizar el producto en la base de datos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Producto - Panel Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container py-5" style="max-width: 600px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Editar Producto</h2>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">← Volver a la tienda</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="update.php?id=<?php echo $product['id']; ?>" method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="marca" class="form-label">Marca *</label>
            <input type="text" class="form-control" id="marca" name="marca" value="<?php echo htmlspecialchars($product['marca']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="modelo" class="form-label">Modelo *</label>
            <input type="text" class="form-control" id="modelo" name="modelo" value="<?php echo htmlspecialchars($product['modelo']); ?>" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="precio" class="form-label">Precio ($) *</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?php echo htmlspecialchars($product['precio']); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="stock" class="form-label">Stock *</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría *</label>
            <select class="form-select" id="categoria_id" name="categoria_id" required>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['id']); ?>" <?php echo ($cat['id'] === $product['categoria_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($product['descripcion']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-warning w-100">Guardar Cambios</button>
    </form>
</body>

</html>