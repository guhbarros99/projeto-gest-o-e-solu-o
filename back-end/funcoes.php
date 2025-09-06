<?php
// funcoes.php

/**
 * Função para buscar um termo em várias tabelas do banco de dados com paginação.
 *
 * @param PDO $pdo Uma instância ativa de conexão PDO com o banco de dados.
 * @param string $termoBusca O texto que será procurado.
 * @param int $pagina O número da página dos resultados a ser exibida. Padrão é 1.
 * @return array Retorna um array com os resultados encontrados ou um array vazio se nada for encontrado ou em caso de erro.
 */
function buscarEmTodoBanco(PDO $conn, string $termoBusca, int $pagina = 1): array
{
    // Define a quantidade de itens por página
    $itensPorPagina = 5;
    
    // Calcula o OFFSET para a paginação
    $offset = ($pagina - 1) * $itensPorPagina;

    // O seu código SQL completo.
    $sql = "
        (SELECT 'Empresa' AS tipo_resultado, id AS id_encontrado, nome AS texto_encontrado, 'empresa.nome' AS origem FROM empresa WHERE nome LIKE ?)
        UNION ALL
        (SELECT 'Empresa' AS tipo_resultado, id AS id_encontrado, email AS texto_encontrado, 'empresa.email' AS origem FROM empresa WHERE email LIKE ?)
        UNION ALL
        (SELECT 'Campo' AS tipo_resultado, id AS id_encontrado, nome AS texto_encontrado, 'campo.nome' AS origem FROM campo WHERE nome LIKE ?)
        UNION ALL
        (SELECT 'Campo' AS tipo_resultado, id AS id_encontrado, descricao AS texto_encontrado, 'campo.descricao' AS origem FROM campo WHERE descricao LIKE ?)
        UNION ALL
        (SELECT 'Módulo' AS tipo_resultado, id AS id_encontrado, nome AS texto_encontrado, 'modulo.nome' AS origem FROM modulo WHERE nome LIKE ?)
        UNION ALL
        (SELECT 'Card' AS tipo_resultado, id AS id_encontrado, titulo AS texto_encontrado, 'cards.titulo' AS origem FROM cards WHERE titulo LIKE ?)
        UNION ALL
        (SELECT 'Dado' AS tipo_resultado, id AS id_encontrado, valor AS texto_encontrado, 'dados.valor' AS origem FROM dados WHERE valor LIKE ?)
        UNION ALL
        (SELECT 'Submódulo' AS tipo_resultado, id AS id_encontrado, nome AS texto_encontrado, 'submodulo.nome' AS origem FROM submodulo WHERE nome LIKE ?)
        UNION ALL
        (SELECT 'Item Submódulo' AS tipo_resultado, id AS id_encontrado, nome AS texto_encontrado, 'item_submodulo.nome' AS origem FROM item_submodulo WHERE nome LIKE ?)
        UNION ALL
        (SELECT 'Venda Flexível' AS tipo_resultado, id AS id_encontrado, CAST(dados_venda AS CHAR) AS texto_encontrado, 'vendas_flexivel.dados_venda' AS origem FROM vendas_flexivel WHERE dados_venda LIKE ?)
        LIMIT :limit OFFSET :offset
    ";

    try {
        $stmt = $conn->prepare($sql);
        $termoComCuringa = '%' . $termoBusca . '%';

        // Associa o mesmo termo a todos os 10 placeholders (?)
        for ($i = 1; $i <= 10; $i++) {
            $stmt->bindValue($i, $termoComCuringa);
        }
        
        // Associa os valores de paginação (LIMIT e OFFSET)
        $stmt->bindParam(':limit', $itensPorPagina, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Em caso de erro, você pode registrar o erro em um log
        // error_log("Erro na busca: " . $e->getMessage());
        return [];
    }
}