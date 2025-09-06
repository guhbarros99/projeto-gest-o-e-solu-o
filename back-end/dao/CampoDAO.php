<!-- -- Tabela campo
CREATE TABLE campo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    nivel INT NOT NULL,
    cor VARCHAR(7), -- Ex: #FFFFFF
    id_empresa INT NOT NULL,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa) ON DELETE CASCADE
); -->

<?php
require_once __DIR__ . '/../database/database.php';

class CampoDAO{
    private $conn;

// CÓDIGO CORRETO ✅
public function __construct() {
    // O nome correto do método é getConn()
    $this->conn = Database::getInstance()->getConn(); 
}

    public function cadastrarCampo(Campo $campo) {
        $query = "INSERT INTO campo (nome, descricao, nivel, cor, id_empresa) VALUES (:nome, :descricao, :nivel, :cor, :id_empresa)";
        $stmt = $this->conn->prepare($query);

        $nome = $campo->getNome();
        $descricao = $campo->getDescricao();
        $nivel = $campo->getNivel();
        $cor = $campo->getCor();
        $id_empresa = $campo->getIdEmpresa();

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':nivel', $nivel);
        $stmt->bindParam(':cor', $cor);
        $stmt->bindParam(':id_empresa', $id_empresa);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function listarCamposPorEmpresa(int $id_empresa) {
    $query = "SELECT * FROM campo WHERE id_empresa = :id_empresa";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id_empresa', $id_empresa);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // pegar como array associativo
    $campos = [];

    foreach ($rows as $row) {
        $campos[] = new Campo(
            (int)$row['id'],
            $row['nome'],
            $row['descricao'],
            (int)$row['nivel'],
            $row['cor'],
            (int)$row['id_empresa']
        );
    }

    return $campos; // retorna array de objetos Campo
}

    public function buscarCampoPorId(int $id) {
        $query = "SELECT * FROM campo WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Campo(
                (int)$row['id'],
                $row['nome'],
                $row['descricao'],
                (int)$row['nivel'],
                $row['cor'],
                (int)$row['id_empresa']
            );
        } else {
            return null; // Campo não encontrado
        }
    }

    public function atualizarCampo(Campo $campo) {
        $query = "UPDATE campo SET nome = :nome, descricao = :descricao, nivel = :nivel, cor = :cor WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $id = $campo->getIdCampo();
        $nome = $campo->getNome();
        $descricao = $campo->getDescricao();
        $nivel = $campo->getNivel();
        $cor = $campo->getCor();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':nivel', $nivel);
        $stmt->bindParam(':cor', $cor);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function excluirCampo(int $id) {
        $query = "DELETE FROM campo WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    function adicionarCamposPadrao(int $id_empresa) {
    $conn = Database::getInstance();

    $camposPadrao = [
        ['nome' => 'Funcionarios', 'descricao' => 'Gestão de funcionários', 'nivel' => 1, 'cor' => '#FF5733'],
        ['nome' => 'Produtos', 'descricao' => 'Gestão de produtos', 'nivel' => 1, 'cor' => '#33FF57'],
        ['nome' => 'Clientes', 'descricao' => 'Gestão de clientes', 'nivel' => 1, 'cor' => '#3357FF'],
        ['nome' => 'Fornecedores', 'descricao' => 'Gestão de fornecedores', 'nivel' => 1, 'cor' => '#F1C40F'],
        ['nome' => 'Agenda', 'descricao' => 'Controle de agenda', 'nivel' => 1, 'cor' => '#9B59B6'],
    ];

    $query = "INSERT INTO campo (nome, descricao, nivel, cor, id_empresa) 
              VALUES (:nome, :descricao, :nivel, :cor, :id_empresa)";
    $stmt = $conn->prepare($query);

    foreach ($camposPadrao as $c) {
        $stmt->bindParam(':nome', $c['nome']);
        $stmt->bindParam(':descricao', $c['descricao']);
        $stmt->bindParam(':nivel', $c['nivel']);
        $stmt->bindParam(':cor', $c['cor']);
        $stmt->bindParam(':id_empresa', $id_empresa);

        $stmt->execute();
    }

    echo "Campos padrão adicionados com sucesso!";
}
}


?>