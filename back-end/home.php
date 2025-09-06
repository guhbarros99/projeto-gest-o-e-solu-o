<?php
session_start();

// --- AUTENTICAÇÃO E INCLUDES ---
if (!isset($_SESSION['token'])) {
    header('Location: cadastro/Cadastro.php');
    exit(); // CORREÇÃO: O echo desnecessário foi removido.
}

// Includes dos arquivos
require_once __DIR__ . '/dao/CampoDAO.php';
require_once __DIR__ . '/model/Campo.php';
require_once __DIR__ . '/dao/ModuloDAO.php';
require_once __DIR__ . '/model/Modulo.php';
require_once __DIR__ . '/dao/CardDAO.php';
require_once __DIR__ . '/model/Card.php';
require_once __DIR__ . '/dao/DadosDAO.php';
require_once __DIR__ . '/model/Dados.php';
require_once __DIR__ . '/dao/EmpresaDAO.php';
require_once __DIR__ . '/model/Empresa.php';
require_once __DIR__ . '/dao/ImagemController.php'; // Considerar renomear para ImagemDAO para consistência
require_once __DIR__ . '/model/Logo.php';
require_once __DIR__ . '/funcoes.php';

// CORREÇÃO: Todo este bloco de lógica foi movido para dentro das tags PHP para ser executado.

// --- LÓGICA DE BUSCA DE DADOS ---

// Inicialização dos DAOs
$camposDAO = new CampoDAO();
$dadosDAO = new DadosDAO();
$cardsDAO = new CardDAO();
$moduloDAO = new ModuloDAO();
$empresaDAO = new EmpresaDAO();
$logoController = new ImagemController();

// Busca de dados com base na sessão
$idEmpresa = $_SESSION['id_empresa'];
$logo = $logoController->getImagemPorEmpresa($idEmpresa);
$campos = $camposDAO->listarCamposPorEmpresa($idEmpresa);
$empresa = $empresaDAO->buscarEmpresaPorId($idEmpresa);

// Lógica para carregar módulos e cards
$modulos = [];
$modulo = null;
if (isset($_GET['id'])) {
    $modulos = $moduloDAO->listarModulosPorCampo($_GET['id'], $idEmpresa);
}
if (isset($_GET['id_modulo'])) {
    $modulo = $moduloDAO->getById($_GET['id_modulo']);
}


// --- LÓGICA DA BUSCA GLOBAL ---
$termo_pesquisado = $_GET['busca'] ?? '';
$pagina_atual = (int)($_GET['pagina'] ?? 1);

// CORREÇÃO: Vamos criar dois arrays para separar os resultados
$resultados_modulos = [];
$resultados_outros = [];

if (!empty($termo_pesquisado)) {
    $conexao = Database::getInstance()->getConn();
    $todos_resultados = buscarEmTodoBanco($conexao, $termo_pesquisado, $pagina_atual);

    // CORREÇÃO: Percorremos os resultados e separamo-los
    foreach ($todos_resultados as $item) {
        if ($item['tipo_resultado'] === 'Módulo') {
            $resultados_modulos[] = $item;
        } else {
            $resultados_outros[] = $item;
        }
    }
}
// Define o caminho do logo com uma imagem padrão caso não encontre
$logoPath = ($logo && file_exists($logo->getCaminho()))
    ? $logo->getCaminho()
    : "https://static.vecteezy.com/ti/vetor-gratis/p1/5538023-forma-simples-montanha-preto-branco-circulo-logo-simbolo-icone-design-grafico-ilustracao-ideia-criativo-vetor.jpg";

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão & Solução</title>
    <link rel="stylesheet" href="../css/styles3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/graficos.css">
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <a href="configuracao.php">
                    <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo">
                </a>
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
            <div class="title">
                <h1><?= $empresa ? htmlspecialchars($empresa->getNome()) : "Gestão & Solução" ?></h1>
            </div>
            <div class="search-bar">
                <form action="home.php" method="GET">
                    <i class="fas fa-search"></i> <input type="text" name="busca" placeholder="Pesquisa" value="<?= htmlspecialchars($termo_pesquisado) ?>" required>
                </form>
            </div>
        </header>

        <aside class="sidebar">
            <?php foreach ($campos as $campo): ?>
                <a href="?id=<?= $campo->getIdCampo(); ?>">
                    <nav>
                        <ul>
                            <li style="box-shadow: 3px 3px 1px <?= htmlspecialchars($campo->getCor()); ?>;border: 1px solid black">
                                <?= htmlspecialchars($campo->getNome()); ?>
                            </li>
                        </ul>
                    </nav>
                </a>
            <?php endforeach; ?>
            <div class="add-button">
                <a href="acoes/Addcampo.php"><i class="fas fa-plus-circle"></i></a>
            </div>
        </aside>

        <main class="main-content">

           <?php if (!empty($termo_pesquisado)): ?>
                
                <h2>Resultados da busca por "<?= htmlspecialchars($termo_pesquisado) ?>"</h2>

                <?php if (!empty($resultados_modulos)): ?>
                    <div class="modulos-encontrados">
                        <h3>Módulos Encontrados:</h3>
                        <ul>
                            <?php foreach ($resultados_modulos as $mod): ?>
                                <li><a href="?id_modulo=<?= htmlspecialchars($mod['id_encontrado']); ?>"><?= htmlspecialchars($mod['texto_encontrado']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($resultados_outros)): ?>
                    <div class="outros-resultados">
                        <h3>Outros Resultados:</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>ID</th>
                                    <th>Texto Encontrado</th>
                                    <th>Origem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultados_outros as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['tipo_resultado']) ?></td>
                                        <td><?= htmlspecialchars($item['id_encontrado']) ?></td>
                                        <td><?= htmlspecialchars($item['texto_encontrado']) ?></td>
                                        <td><?= htmlspecialchars($item['origem']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if (empty($resultados_modulos) && empty($resultados_outros)): ?>
                    <p class="not-found">Nenhum resultado encontrado para a sua busca.</p>
                <?php endif; ?>
                
                <?php else: ?>
                <?php if ($modulo): ?>
                    <h2>Detalhes do Módulo: <?= htmlspecialchars($modulo->getNome()); ?></h2>
                <?php elseif (!empty($modulos)): ?>
                     <h2>Módulos:</h2>
                     <ul>
                        <?php foreach ($modulos as $mod): ?>
                           <li><a href="?id_modulo=<?= $mod->getId(); ?>"><?= htmlspecialchars($mod->getNome()); ?></a></li>
                        <?php endforeach; ?>
                     </ul>
                <?php else: ?>
                    <p>Bem-vindo! Selecione um campo na barra lateral ou utilize a busca.</p>
                <?php endif; ?>
            <?php endif; ?>


            <ul>
                <?php foreach ($modulos as $mod): ?>
                    <li><a href="?id=<?= $_GET['id'] ?? '' ?>&id_modulo=<?= $mod->getId(); ?>"><?= htmlspecialchars($mod->getNome()); ?></a></li>
                <?php endforeach; ?>
            </ul>

            <?php if (isset($_GET['id'])): ?>
                <a href="acoes/Adicionarmodulo.php?id_campo=<?= $_GET['id']; ?>">Adicionar Módulo</a>
            <?php endif; ?>


            <?php if (isset($_GET['id_modulo']) && $modulo): // CORREÇÃO: Adicionada verificação se $modulo não é nulo 
            ?>
                <div class="cards-table">
                    <div class="profile-box">
                        <h2 class="profile-title"><?= htmlspecialchars($modulo->getNome()); ?></h2>
                        <div class="profile-grid">
                            <div class="profile-group"><label>Nome</label><input type="text" value="Herbert" readonly></div>
                            <div class="profile-group"><label>Função</label><input type="text" value="Entregador" readonly></div>
                            <div class="profile-group"><label>Gmail</label><input type="email" value="Herbert@gmail.com" readonly></div>
                            <div class="profile-group"><label>Salário</label><input type="text" value="R$ 2.000,00" readonly></div>
                            <div class="profile-group"><label>Contato</label><input type="tel" value="11 1234 1234" readonly></div>
                        </div>
                        <a href="acoes/Adicionarsubmodulo.php?id_modulo=<?= $_GET['id_modulo']; ?>"><button class="profile-edit-button">Editar</button></a>
                        <a href="home.php"><button class="back">Voltar</button></a>
                    </div>
                    <div class="chart-container">
                        <h2 class="chart-title">Gráfico de Vendas</h2>
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Seu Javascript aqui (já estava correto)
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('closed');
                });
            }
        });

        // O código do gráfico só deve rodar se o elemento canvas existir
        const canvas = document.getElementById('myChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro'],
                    datasets: [{
                        label: 'Vendas',
                        data: [50, 19, 3, 5, 2, 17, 8, 54, 2],
                        backgroundColor: 'rgba(255, 139, 128, 0.7)',
                        borderColor: 'rgba(255, 139, 128, 1)',
                        borderWidth: 2,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: "#fff"
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: "#fff"
                            },
                            grid: {
                                color: "rgba(255,255,255,0.2)"
                            }
                        },
                        y: {
                            ticks: {
                                color: "#fff"
                            },
                            grid: {
                                color: "rgba(255,255,255,0.2)"
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>