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
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4", $this->username, $this->password);
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

    public function getById($id) {
        try {
            $sql = "SELECT * FROM customers WHERE id = :id";
            $query = $this->conn->prepare($sql);
            $query->execute(['id' => $id]);

            return $query->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die("Erro na consulta: " . $e->getMessage());
        }
    }

    public function insertCustomer($data) {
        try {
            $data = (object) $data;
            $sql = "INSERT INTO customers (id, name, address, city, phone) VALUES (null, :name, :address, :city, :phone)";
            $insert = $this->conn->prepare($sql);

            if ($insert->execute(['name' => $data->name, 'address' => $data->address, 'city' => $data->city, 'phone' => $data->phone])) {
                return true;
            }

            return false;
        } catch (PDOException $e) {
            die("Erro na inserção: " . $e->getMessage());
        }
    }

    public function updateCustomer($data, $id) {
        try {
            $data = (object) $data;
            $sql = "UPDATE customers SET name = :name, address = :address, city = :city, phone = :phone WHERE id = :id";
            $update = $this->conn->prepare($sql);

            if ($update->execute(['name' => $data->name, 'address' => $data->address, 'city' => $data->city, 'phone' => $data->phone, 'id' => $id])) {
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            die("Erro na atualização: " . $e->getMessage());
        }
    }

    public function deleteCustomer($id) {
        try {
            $sql = "DELETE FROM customers WHERE id = :id";
            $update = $this->conn->prepare($sql);

            if ($update->execute(['id' => $id])) {
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            die("Erro na atualização: " . $e->getMessage());
        }
    }
}
?>
