<?php

// CREATE TABLE empresa (
//     id_empresa INT AUTO_INCREMENT PRIMARY KEY,
//     nome VARCHAR(100) NOT NULL,
//     email VARCHAR(150) NOT NULL UNIQUE,
//     cnpj VARCHAR(18) NOT NULL UNIQUE,
//     senha VARCHAR(255) NOT NULL,
//     token VARCHAR(255) NULL
// );
require_once __DIR__ . '/../database/Database.php';
class EmpresaDAO
{

    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConn();
    }

    public function buscarEmpresaPorEmail(string $email)
    {
        $query = "SELECT * FROM empresa WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Empresa(
                $row['id'],
                $row['nome'],
                $row['email'],
                $row['cnpj'],
                $row['senha'],
                $row['token']
            );
        }
        return null;
    }

    public function buscarEmpresaPorCnpj(string $cnpj)
    {
        $query = "SELECT * FROM empresa WHERE cnpj = :cnpj";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Empresa(
                $row['id'],
                $row['nome'],
                $row['email'],
                $row['cnpj'],
                $row['senha'],
                $row['token']
            );
        }
        return null;
    }

    public function getAllEmpresas()
    {
        $query = "SELECT * FROM empresa";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $empresas = $stmt->fetchAll(PDO::FETCH_CLASS, 'Empresa');
        return $empresas ?: [];
    }

    public function buscarEmpresaPorId(int $id)
    {
        $query = "SELECT * FROM empresa WHERE id= :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Empresa(
                $row['id'],
                $row['nome'],
                $row['email'],
                $row['cnpj'],
                $row['senha'],
                $row['token']
            );
        }
        return null;
    }





    public function cadastrarEmpresa(Empresa $empresa)
    {
        $query = "INSERT INTO empresa (nome, email, cnpj, senha, token) VALUES (:nome, :email, :cnpj, :senha, :token)";
        $stmt = $this->conn->prepare($query);

        $nome = $empresa->getNome();
        $email = $empresa->getEmail();
        $cnpj = $empresa->getCnpj();
        $senha = password_hash($empresa->getSenha(), PASSWORD_DEFAULT);
        $token = $empresa->getToken();

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':token', $token);


        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function verificarEmpresaCnpjExistente(string $cnpj): bool
    {
        $query = "SELECT COUNT(*) FROM empresa WHERE cnpj = :cnpj";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    public function verificarEmpresaEmailExistente(string $email): bool
    {
        $query = "SELECT COUNT(*) FROM empresa WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    public function UpdateEmpresa(Empresa $empresa)
    {
        $query = "UPDATE empresa SET nome = :nome, email = :email, cnpj = :cnpj, senha = :senha, token = :token WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $id = $empresa->getIdEmpresa();
        $nome = $empresa->getNome();
        $email = $empresa->getEmail();
        $cnpj = $empresa->getCnpj();
        $senha = $empresa->getSenha();
        $token = $empresa->getToken();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cnpj', $cnpj);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':token', $token);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
