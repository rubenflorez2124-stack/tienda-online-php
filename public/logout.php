<?php
// public/logout.php
require_once __DIR__ . '/../src/services/SessionService.php';

// Asegurar que la sesión esté iniciada antes de destruirla
SessionService::init();

// Destruir todas las variables de sesión y la cookie de sesión si existe
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesión por completo
session_destroy();

// Redirigir al inicio
header('Location: index.php');
exit;
