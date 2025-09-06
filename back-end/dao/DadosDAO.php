<!-- CREATE TABLE dados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    valor VARCHAR(255) NOT NULL,
    id_card INT NOT NULL,
    FOREIGN KEY (id_card) REFERENCES cards(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB; -->


<?php

require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../model/Dados.php';

class DadosDAO
{
    private $conn;

    public function __construct()
    {
            $this->conn = Database::getInstance()->getConn();
    }

    public function cadastrarDado(Dados $dados): bool
    {
        $sql = "INSERT INTO dados (valor, id_card) VALUES (:valor, :id_card)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':valor', $dados->getValor());
        $stmt->bindValue(':id_card', $dados->getIdCard());
        return $stmt->execute();
    }

    public function listarDadosPorCard(int $id_card): array
    {
        $sql = "SELECT * FROM dados WHERE id_card = :id_card";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_card', $id_card);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dadosList = [];
        foreach ($result as $row) {
            $dadosList[] = new Dados($row['id'], $row['valor'], $row['id_card']);
        }
        return $dadosList;
    }
    public function buscarDadosPorCard(int $id_card): array
    {
        $sql = "SELECT * FROM dados WHERE id_card = :id_card";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_card', $id_card);
        $stmt->execute();
        $dadosList = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dadosList[] = new Dados($row['id'], $row['valor'], $row['id_card']);
        }
        return $dadosList;
    }
}

?>