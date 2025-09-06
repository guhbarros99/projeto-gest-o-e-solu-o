<?php
require_once '../database/database.php';
require_once 'Valor.php'; // classe Valor com os atributos necessários

class ValorDAO {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConn();
    }

    // --- CREATE ---
    public function create(Valor $valor, string $tipo) {
        if ($tipo === 'texto') {
            $sql = "INSERT INTO item_submodulo (nome, id_submodulo) VALUES (:nome, :id_submodulo)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nome', $valor->valor); // assumindo que $valor->valor é o texto
            $stmt->bindValue(':id_submodulo', $valor->id_submodulo);
        } else { // numero
            $sql = "INSERT INTO valor_submodulo (valor, id_submodulo) VALUES (:valor, :id_submodulo)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':valor', $valor->valor, PDO::PARAM_INT);
            $stmt->bindValue(':id_submodulo', $valor->id_submodulo);
        }
        return $stmt->execute();
    }

    // --- READ ---
    public function getById(int $id, string $tipo) {
        if ($tipo === 'texto') {
            $sql = "SELECT * FROM item_submodulo WHERE id = :id";
        } else {
            $sql = "SELECT * FROM valor_submodulo WHERE id = :id";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll(string $tipo) {
        if ($tipo === 'texto') {
            $sql = "SELECT * FROM item_submodulo";
        } else {
            $sql = "SELECT * FROM valor_submodulo";
        }
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- UPDATE ---
    public function update(Valor $valor, string $tipo) {
        if ($tipo === 'texto') {
            $sql = "UPDATE item_submodulo SET nome = :nome, id_submodulo = :id_submodulo WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nome', $valor->valor);
            $stmt->bindValue(':id_submodulo', $valor->id_submodulo);
            $stmt->bindValue(':id', $valor->id, PDO::PARAM_INT);
        } else {
            $sql = "UPDATE valor_submodulo SET valor = :valor, id_submodulo = :id_submodulo WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':valor', $valor->valor, PDO::PARAM_INT);
            $stmt->bindValue(':id_submodulo', $valor->id_submodulo);
            $stmt->bindValue(':id', $valor->id, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }

    // --- DELETE ---
    public function delete(int $id, string $tipo) {
        if ($tipo === 'texto') {
            $sql = "DELETE FROM item_submodulo WHERE id = :id";
        } else {
            $sql = "DELETE FROM valor_submodulo WHERE id = :id";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
