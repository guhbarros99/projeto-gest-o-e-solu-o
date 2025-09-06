<?php
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../model/Modulo.php';
class ModuloDAO
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConn();
    }

    public function adicionarModulo($modulo)
    {
        $sql = "INSERT INTO modulo (nome, id_campo, id_empresa) VALUES (:nome, :id_campo, :id_empresa)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nome'      => $modulo->getNome(),
            ':id_campo'  => $modulo->getIdCampo(),
            ':id_empresa' => $modulo->getIdEmpresa()
        ]);
    }

    public function listarModulosPorEmpresa($id_empresa)
    {
        $sql = "SELECT * FROM modulo WHERE id_empresa = :id_empresa";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_empresa' => $id_empresa]);

        $modulos = [];
        while ($row = $stmt->fetch()) {
            $modulos[] = new Modulo(
                $row['id'],
                $row['nome'],
                $row['id_campo'],
                $row['id_empresa']
            );
        }

        return $modulos;
    }

    public function listarModulosPorCampo($id_campo, $id_empresa)
    {
        $sql = "SELECT * FROM modulo WHERE id_campo = :id_campo AND id_empresa = :id_empresa";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_campo' => $id_campo, ':id_empresa' => $id_empresa]);

        $modulos = [];
        while ($row = $stmt->fetch()) {
            $modulos[] = new Modulo(
                $row['id'],
                $row['nome'],
                $row['id_campo'],
                $row['id_empresa']
            );
        }

        return $modulos;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM modulo WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();

        if ($row) {
            return new Modulo(
                $row['id'],
                $row['nome'],
                $row['id_campo'],
                $row['id_empresa']
            );
        }

        return null; // caso não encontre o módulo
    }
}
