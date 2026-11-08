<?php
// src/services/UserService.php
require_once __DIR__ . '/../config/Config.php';

class UserService
{

    // Autenticar un usuario por email y contraseña
    public static function authenticate(string $email, string $password)
    {
        try {
            $db = Config::getConnection();
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si el usuario existe y la contraseña coincide con el hash
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Registrar un nuevo usuario de forma segura
    public static function register(string $username, string $email, string $password, string $role = 'client')
    {
        try {
            $db = Config::getConnection();

            // Comprobar si el correo ya está registrado
            $stmtCheck = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmtCheck->execute([$email]);
            if ($stmtCheck->fetch()) {
                return ['success' => false, 'message' => 'El correo electrónico ya está registrado.'];
            }

            // Encriptar la contraseña de forma segura
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertar nuevo usuario
            $stmt = $db->prepare("INSERT INTO usuarios (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $role]);

            return ['success' => true, 'message' => 'Usuario registrado con éxito.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    // Obtener información de un usuario por su ID
    public static function getUserById(int $id)
    {
        try {
            $db = Config::getConnection();
            $stmt = $db->prepare("SELECT id, username, email, role FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
}
