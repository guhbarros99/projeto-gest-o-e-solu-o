<?php
// CREATE TABLE cards (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     titulo VARCHAR(255) NOT NULL,
//     id_modulo INT NOT NULL,
//     FOREIGN KEY (id_modulo) REFERENCES modulo(id) ON DELETE CASCADE
// );

class CardDAO
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConn();
    }
    public function cadastrarCard(Card $card)
    {
        $sql = "INSERT INTO cards (titulo, id_modulo) VALUES (:titulo, :id_modulo)";
        $stmt = $this->conn->prepare($sql);

        $titulo = $card->getTitulo();
        $id_modulo = $card->getIdModulo();
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':id_modulo', $id_modulo, PDO::PARAM_INT);


        return $stmt->execute();
    }

    public function listarCardsPorModulo($id_modulo)
    {
        $sql = "SELECT * FROM cards WHERE id_modulo = :id_modulo";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_modulo', $id_modulo, PDO::PARAM_INT);
        $stmt->execute();
        $cardsData = $stmt->fetchAll();

        $cards = [];
        foreach ($cardsData as $cardData) {
            $cards[] = new Card($cardData['id'], $cardData['titulo'], $cardData['id_modulo']);
        }
        return $cards;
    }
}
