<?php
// public/create.php
require_once __DIR__ . '/../src/services/SessionService.php';
require_once __DIR__ . '/../src/services/ProductService.php';

SessionService::init();

// Seguridad: Solo los administradores pueden añadir productos
if (!SessionService::isLoggedIn() || SessionService::get('role') !== 'admin') {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Obtener las categorías activas para el select del formulario
$categorias = ProductService::getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');
    $categoria_id = trim($_POST['categoria_id'] ?? '');

    // Validaciones básicas
    if (empty($marca) || empty($modelo) || empty($precio) || empty($stock) || empty($categoria_id)) {
        $error = 'Por favor, completa todos los campos obligatorios.';
    } elseif (!is_numeric($precio) || $precio < 0) {
        $error = 'El precio debe ser un número válido.';
    } elseif (!is_numeric($stock) || $stock < 0) {
        $error = 'El stock debe ser un número entero válido.';
    } else {
        $data = [
            'marca' => $marca,
            'modelo' => $modelo,
            'precio' => (float)$precio,
            'stock' => (int)$stock,
            'descripcion' => $descripcion,
            'imagen' => $imagen,
            'categoria_id' => $categoria_id
        ];

        $result = ProductService::createProduct($data);
        if ($result) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Hubo un error al registrar el producto en la base de datos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Añadir Nuevo Producto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container py-5" style="max-width: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Añadir Nuevo Producto</h1>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">← Volver al catálogo</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="create.php" method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="marca" class="form-label">Marca <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="marca" name="marca" required value="<?php echo htmlspecialchars($_POST['marca'] ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label for="modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="modelo" name="modelo" required value="<?php echo htmlspecialchars($_POST['modelo'] ?? ''); ?>">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="precio" class="form-label">Precio ($) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" required value="<?php echo htmlspecialchars($_POST['precio'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="stock" name="stock" required value="<?php echo htmlspecialchars($_POST['stock'] ?? ''); ?>">
            </div>
        </div>

        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
            <select class="form-select" id="categoria_id" name="categoria_id" required>
                <option value="">Selecciona una categoría...</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['id']); ?>" <?php echo (isset($_POST['categoria_id']) && $_POST['categoria_id'] === $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">URL de la Imagen</label>
            <input type="url" class="form-control" id="imagen" name="imagen" placeholder="https://ejemplo.com/imagen.jpg" value="<?php echo htmlspecialchars($_POST['imagen'] ?? ''); ?>">
            <div class="form-text">Si se deja vacío, se asignará una imagen por defecto automáticamente.</div>
        </div>

        <div class="mb-4">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">Guardar Producto</button>
    </form>
</body>

</html>