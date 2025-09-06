<?php
// back-end/funcoes.php

function buscarEmTodoBanco(PDO $conn, string $termoBusca, int $pagina = 1): array
{
    $itensPorPagina = 5;
    $offset = ($pagina - 1) * $itensPorPagina;

    // SQL FINAL E CORRIGIDO:
    // Garante que a segunda e terceira colunas de CADA SELECT são nomeadas
    // como 'id_encontrado' e 'texto_encontrado' para corresponder ao HTML.
    $sql = "
        (SELECT 'Empresa' AS tipo_resultado, id AS id_encontrado, nome COLLATE utf8mb4_unicode_ci AS texto_encontrado, 'empresa.nome' AS origem FROM empresa WHERE nome LIKE ?)
        UNION ALL
        (SELECT 'Empresa' AS tipo_resultado, id AS id_encontrado, email COLLATE utf8mb4_unicode_ci AS texto_encontrado, 'empresa.email' AS origem FROM empresa WHERE email LIKE ?)
        UNION ALL
        (SELECT 'Campo' AS tipo_resultado, id AS id_encontrado, nome COLLATE utf8mb4_unicode_ci AS texto_encontrado, 'campo.nome' AS origem FROM campo WHERE nome LIKE ?)
        UNION ALL
        (SELECT 'Campo' AS tipo_resultado, id AS id_encontrado, descricao COLLATE utf8mb4_unicode_ci AS texto_encontrado, 'campo.descricao' AS origem FROM campo WHERE descricao LIKE ?)
        UNION ALL
        (SELECT 'Módulo' AS tipo_resultado, id AS id_encontrado, nome COLLATE utf8mb4_unicode_ci AS texto_encontrado, 'modulo.nome' AS origem FROM modulo WHERE nome LIKE ?)
        UNION ALL
        (SELECT 'Card' AS tipo_resultado, id AS id_encontrado, titulo COLLATE utf8mb4_unicode_ci AS texto_encontrado, 'cards.titulo' AS origem FROM cards WHERE titulo LIKE ?)
        UNION ALL
        (SELECT 'Dado' AS tipo_resultado, id AS id_encontrado, valor COLLATE utf8mb4_unicode_ci AS texto_encontrado, 'dados.valor' AS origem FROM dados WHERE valor LIKE ?)
        UNION ALL
        (SELECT 'Submódulo' AS tipo_resultado, id AS id_encontrado, nome COLLATE utf8mb4_unicode_ci AS texto_encontrado, 'submodulo.nome' AS origem FROM submodulo WHERE nome LIKE ?)
        UNION ALL
        (SELECT 'Item Submódulo' AS tipo_resultado, id AS id_encontrado, nome COLLATE utf8mb4_unicode_ci AS texto_encontrado, 'item_submodulo.nome' AS origem FROM item_submodulo WHERE nome LIKE ?)
        UNION ALL
        (SELECT 'Venda Flexível' AS tipo_resultado, id AS id_encontrado, CAST(dados_venda AS CHAR) COLLATE utf8mb4_unicode_ci AS texto_encontrado, 'vendas_flexivel.dados_venda' AS origem FROM vendas_flexivel WHERE dados_venda LIKE ?)
        LIMIT ? OFFSET ?
    ";

    try {
        $stmt = $conn->prepare($sql);
        $termoComCuringa = '%' . $termoBusca . '%';

        for ($i = 1; $i <= 10; $i++) {
            $stmt->bindValue($i, $termoComCuringa);
        }
        
        $stmt->bindValue(11, $itensPorPagina, PDO::PARAM_INT);
        $stmt->bindValue(12, $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erro na busca global (funcoes.php): " . $e->getMessage());
        return [];
    }
}