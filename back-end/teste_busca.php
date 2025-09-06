<?php
// back-end/teste_busca.php

// Ativa a exibição de TODOS os erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Iniciando Teste de Busca Direta...</h1>";

// Inclui a classe de conexão com o banco de dados
require_once __DIR__ . '/database/Database.php';

try {
    echo "<p>Tentando obter conexão com o banco de dados...</p>";
    
    // 1. Obter a conexão
    $conn = Database::getInstance()->getConn();
    
    echo "<p style='color: green;'>Conexão obtida com sucesso!</p>";
    
    // 2. Definir um termo de busca que SABEMOS que existe
    $termoBusca = "Financeiro";
    $termoComCuringa = '%' . $termoBusca . '%';
    
    echo "<p>Buscando pelo termo: '<b>" . htmlspecialchars($termoBusca) . "</b>' na tabela <b>'modulo'</b>...</p>";
    
    // 3. ISOLAR UMA ÚNICA PARTE DA SUA CONSULTA ORIGINAL
    // Vamos testar apenas a busca na tabela 'modulo'
    $sql = "SELECT 'Módulo' AS tipo_resultado, id AS id_encontrado, nome AS texto_encontrado, 'modulo.nome' AS origem FROM modulo WHERE nome LIKE ?";
    
    echo "<p>Executando a seguinte consulta SQL:</p>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    
    // 4. Preparar e Executar
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(1, $termoComCuringa);
    $stmt->execute();
    
    // 5. Obter os resultados
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Resultados:</h2>";
    
    if (empty($resultados)) {
        echo "<p style='color: red; font-weight: bold;'>Nenhum resultado encontrado. Verifique os seguintes pontos:</p>";
        echo "<ul>";
        echo "<li>O termo '<b>" . htmlspecialchars($termoBusca) . "</b>' realmente existe na coluna 'nome' da tabela 'modulo'?</li>";
        echo "<li>O nome da tabela ('modulo') e da coluna ('nome') estão escritos exatamente como no seu banco de dados? (Cuidado com singular/plural, letras maiúsculas/minúsculas).</li>";
        echo "<li>Os dados de exemplo foram inseridos corretamente no banco de dados?</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: green; font-weight: bold;'>" . count($resultados) . " resultado(s) encontrado(s)! A conexão e a consulta estão funcionando.</p>";
        echo "<pre>";
        print_r($resultados);
        echo "</pre>";
    }

} catch (PDOException $e) {
    // Se houver qualquer erro na conexão ou na consulta, ele será mostrado aqui.
    echo "<h2 style='color: red;'>ERRO FATAL NO TESTE:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}