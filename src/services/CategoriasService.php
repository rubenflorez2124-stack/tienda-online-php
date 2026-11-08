<?php
// src/services/CategoriasService.php
require_once __DIR__ . '/../config/Config.php';

class CategoriasService
{

    // Obtener todas las categorías ordenadas alfabéticamente
    public static function getAllCategories()
    {
        try {
            $db = Config::getConnection();
            $stmt = $db->query("SELECT * FROM categorias ORDER BY nombre ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Obtener una categoría por su ID
    public static function getCategoryById($id)
    {
        try {
            $db = Config::getConnection();
            $stmt = $db->prepare("SELECT * FROM categorias WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
}
