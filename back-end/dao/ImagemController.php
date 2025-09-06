<?php
require_once __DIR__ . '/../database/Database.php';

class ImagemController
{
    private $conn;

    public function __construct()
    {
        // Usa PDO
        $this->conn = Database::getInstance()->getConn();
    }

    /**
     * Recebe o arquivo e o id da empresa
     * @param array $arquivo $_FILES['imagem']
     * @param int $idEmpresa ID da empresa associada à imagem
     * @return string|false Caminho da imagem ou false se falhar
     */
    public function uploadImagem($arquivo, int $idEmpresa)
    {
        if ($arquivo['error'] === UPLOAD_ERR_OK) {
            $diretorio = 'uploads/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            $nomeArquivo = basename($arquivo['name']);
            $caminhoDestino = $diretorio . time() . "_" . $nomeArquivo;

            if (move_uploaded_file($arquivo['tmp_name'], $caminhoDestino)) {
                return $this->salvarNoBanco($nomeArquivo, $caminhoDestino, $idEmpresa);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Salva a imagem no banco
     */
    private function salvarNoBanco(string $nomeArquivo, string $caminho, int $idEmpresa)
    {
        try {
            $sql = "INSERT INTO imagens (nome, caminho, id_empresa) VALUES (:nome, :caminho, :id_empresa)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nome', $nomeArquivo);
            $stmt->bindValue(':caminho', $caminho);
            $stmt->bindValue(':id_empresa', $idEmpresa, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $caminho;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    public function getImagemPorEmpresa(int $idEmpresa)
    {
        $sql = "SELECT * FROM imagens WHERE id_empresa = :id_empresa ORDER BY criado_em DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_empresa', $idEmpresa, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Logo(
                $row['id'],
                $row['nome'],
                $row['caminho'],
                $row['criado_em']
            );
        }
        return null;
    }

    /**
     * Atualiza a imagem de uma empresa, apagando a antiga.
     * @param array $arquivo Arquivo enviado ($_FILES['imagem'])
     * @param int $idEmpresa ID da empresa
     * @return string|false Caminho da nova imagem ou false se falhar
     */
    public function atualizarImagem($arquivo, int $idEmpresa)
    {
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $diretorio = 'uploads/';
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $nomeArquivo = basename($arquivo['name']);
        $caminhoDestino = $diretorio . time() . "_" . $nomeArquivo;

        if (!move_uploaded_file($arquivo['tmp_name'], $caminhoDestino)) {
            return false;
        }

        try {
            // Pega imagem existente
            $sql = "SELECT caminho FROM imagens WHERE id_empresa = :id_empresa";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_empresa', $idEmpresa, PDO::PARAM_INT);
            $stmt->execute();
            $imagemAntiga = $stmt->fetch(PDO::FETCH_ASSOC);

            // Apaga arquivo antigo
            if ($imagemAntiga && file_exists($imagemAntiga['caminho'])) {
                unlink($imagemAntiga['caminho']);
            }

            // Atualiza registro no banco
            $sqlUpdate = "UPDATE imagens SET nome = :nome, caminho = :caminho, criado_em = NOW() WHERE id_empresa = :id_empresa";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bindValue(':nome', $nomeArquivo);
            $stmtUpdate->bindValue(':caminho', $caminhoDestino);
            $stmtUpdate->bindValue(':id_empresa', $idEmpresa, PDO::PARAM_INT);

            if ($stmtUpdate->execute()) {
                return $caminhoDestino;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
