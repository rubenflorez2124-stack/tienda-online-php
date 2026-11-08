<?php
// src/services/ProductService.php
require_once __DIR__ . '/../config/Config.php';

class ProductService
{

    // Obtener todos los productos no eliminados
    public static function getAllProducts()
    {
        try {
            $db = Config::getConnection();
            $stmt = $db->prepare("SELECT * FROM productos WHERE is_deleted = false ORDER BY id ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Obtener un producto específico por su ID
    public static function getProductById($id)
    {
        try {
            $db = Config::getConnection();
            $stmt = $db->prepare("SELECT * FROM productos WHERE id = ? AND is_deleted = false");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    // Obtener las categorías disponibles para el formulario
    public static function getCategories()
    {
        try {
            $db = Config::getConnection();
            $stmt = $db->prepare("SELECT id, nombre FROM categorias WHERE is_deleted = false");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Registrar un nuevo producto en la base de datos
    public static function createProduct($data)
    {
        try {
            $db = Config::getConnection();

            // Generar un UUID v4 válido para la base de datos
            $uuid = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            );

            $stmt = $db->prepare("
                INSERT INTO productos (marca, modelo, precio, stock, descripcion, imagen, categoria_id, uuid) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['marca'],
                $data['modelo'],
                $data['precio'],
                $data['stock'],
                $data['descripcion'],
                !empty($data['imagen']) ? $data['imagen'] : 'https://via.placeholder.com/150',
                $data['categoria_id'],
                $uuid
            ]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Actualizar un producto existente
    public static function updateProduct($id, $data)
    {
        try {
            $db = Config::getConnection();
            $stmt = $db->prepare("
                UPDATE productos 
                SET marca = ?, modelo = ?, precio = ?, stock = ?, descripcion = ?, categoria_id = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");

            $stmt->execute([
                $data['marca'],
                $data['modelo'],
                $data['precio'],
                $data['stock'],
                $data['descripcion'],
                $data['categoria_id'],
                $id
            ]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Actualizar únicamente la imagen de un producto
    public static function updateProductImage($id, $imagenUrl)
    {
        try {
            $db = Config::getConnection();
            $stmt = $db->prepare("
                UPDATE productos 
                SET imagen = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $stmt->execute([$imagenUrl, $id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
