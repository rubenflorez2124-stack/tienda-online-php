<?php
// src/services/SessionService.php

class SessionService
{

    // Iniciar la sesión de forma segura utilizando ob_start() si es necesario
    public static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Previene problemas de "headers already sent"
            if (!headers_sent()) {
                session_start();
            }
        }
    }

    // Guardar un valor en la sesión (aceptando string como clave)
    public static function set(string $key, $value)
    {
        self::init();
        $_SESSION[$key] = $value;
    }

    // Obtener un valor de la sesión (aceptando string como clave)
    public static function get(string $key)
    {
        self::init();
        return $_SESSION[$key] ?? null;
    }

    // Eliminar un valor de la sesión
    public static function remove(string $key)
    {
        self::init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    // Cerrar sesión por completo
    public static function destroy()
    {
        self::init();
        session_unset();
        session_destroy();
    }

    // Verificar si el usuario ha iniciado sesión
    public static function isLoggedIn(): bool
    {
        self::init();
        return isset($_SESSION['user_id']);
    }

    // Verificar si el usuario actual tiene rol de administrador
    public static function isAdmin(): bool
    {
        self::init();
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}
