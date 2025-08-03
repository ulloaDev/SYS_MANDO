<?php
class Database {
    private static $instance;
    private $connection;

    private function __construct() {
        $this->connection = new PDO(
            'mysql:host=localhost;dbname=sysmanto',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public static function getInstance(): PDO {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
}