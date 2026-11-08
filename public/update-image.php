<?php
// public/update-image.php
require_once __DIR__ . '/../src/services/SessionService.php';
require_once __DIR__ . '/../src/services/ProductService.php';

SessionService::init();

// Validar que el usuario haya iniciado sesión
if (!SessionService::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Obtener el producto actual
$producto = ProductService::getProductById((int)$id);

if (!$producto) {
    header('Location: index.php');
    exit;
}

// Procesar el formulario cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Extensiones permitidas
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Crear un nombre único para evitar conflictos usando time() con paréntesis
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/uploads/';

            // Asegurarse de que la carpeta uploads exista dentro de public/
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Actualizar la ruta de la imagen en la base de datos usando el servicio
                $updated = ProductService::updateProductImage((int)$id, $newFileName);

                if ($updated) {
                    $success = 'Imagen actualizada correctamente.';
                    // Recargar datos actualizados del producto
                    $producto = ProductService::getProductById((int)$id);
                } else {
                    $error = 'Error al actualizar la base de datos.';
                }
            } else {
                $error = 'Error al mover el archivo subido al servidor.';
            }
        } else {
            $error = 'Formato de imagen no permitido. Usa JPG, JPEG, PNG o WEBP.';
        }
    } else {
        $error = 'Por favor, selecciona una imagen válida.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Actualizar Imagen - Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Actualizar Imagen de Producto</h4>
                    </div>
                    <div class="card-body">
                        <h5><?= htmlspecialchars($producto['nombre'] ?? 'Producto') ?></h5>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <form action="update-image.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Seleccionar nueva imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" required>
                            </div>
                            <button type="submit" class="btn btn-success">Subir Imagen</button>
                            <a href="index.php" class="btn btn-secondary">Volver al Catálogo</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>