<?php
class Database {
    private $connection;
    
    public function __construct() {
        $config = include_once'../config/database.php';
        try {
            $this->connection = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function isConnected(): bool {
        return $this->connection !== null;
    }

    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error en consulta: " . $e->getMessage());
        }
    }
}