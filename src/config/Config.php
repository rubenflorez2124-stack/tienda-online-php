<?php
// src/config/Config.php

class Config
{
    public static function getConnection()
    {
        $host = getenv('POSTGRES_HOST') ?: 'postgres-db';
        $db   = getenv('POSTGRES_DB') ?: 'tienda';
        $user = getenv('POSTGRES_USER') ?: 'admin';
        $pass = getenv('POSTGRES_PASSWORD') ?: 'adminPassword123';

        $dsn = "pgsql:host=$host;dbname=$db";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión a la Base de Datos: " . $e->getMessage());
        }
    }
}
