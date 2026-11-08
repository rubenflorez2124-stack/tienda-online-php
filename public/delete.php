<?php
require_once __DIR__ . '/../src/services/SessionService.php';
require_once __DIR__ . '/../src/config/Config.php';

SessionService::init();

// Seguridad: Solo administradores
if (!SessionService::isLoggedIn() || SessionService::get('role') !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $db = Config::getConnection();
        $stmt = $db->prepare("UPDATE productos SET is_deleted = true, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$id]);
    } catch (Exception $e) {
    }
}

header('Location: index.php');
exit;
