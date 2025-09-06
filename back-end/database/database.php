<?php
// back-end/database/Database.php

class Database {
    private static $instance = null;
    private $conn; // <-- Renomeado de $pdo para $conn

    private function __construct() {
        $host = 'localhost';
        $db   = 'gestao';
        $user = 'root';
        $pass = ''; // Sua senha
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // <-- Renomeado de $this->pdo para $this->conn
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // <-- Método renomeado de getPdo() para getConn()
    public function getConn() {
        return $this->conn; // <-- Retorna a propriedade $conn
    }
}