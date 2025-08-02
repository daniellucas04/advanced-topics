<?php

namespace App\Connection;

use PDO;
use PDOException;

class Database {
    private $host;
    private $dbName;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        $this->host     = $_ENV['DB_HOST'];
        $this->dbName   = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USERNAME'];
        $this->password = $_ENV['DB_PASSWORD'];

        try {
            $this->conn = new PDO("sqlsrv:server = $this->host; Database = $this->dbName; TrustServerCertificate = false", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }

    public function listCustomers() {
        try {
            $stmt = $this->conn->query("SELECT * FROM customers");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Erro na consulta: " . $e->getMessage());
        }
    }
}
?>
