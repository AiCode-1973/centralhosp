<?php
require 'auth.php';
include 'config.php';

// Consultas para estatísticas
$total_vencimentos = $conn->query("SELECT COUNT(*) as count FROM vencimentos")->fetch_assoc()['count'];
$total_vencidos = $conn->query("SELECT COUNT(*) as count FROM vencimentos WHERE situacao = 'Vencido'")->fetch_assoc()['count'];
$total_em_dia = $conn->query("SELECT COUNT(*) as count FROM vencimentos WHERE situacao = 'Em dia'")->fetch_assoc()['count'];
$total_pendentes = $conn->query("SELECT COUNT(*) as count FROM vencimentos WHERE situacao = 'Pendente'")->fetch_assoc()['count'];

// Consulta para documentos por mês (campo proxima_data)
$sql_por_mes = "SELECT 
    DATE_FORMAT(proxima_data, '%Y-%m') as mes,
    DATE_FORMAT(proxima_data, '%M/%Y') as mes_nome,
    COUNT(*) as total
FROM vencimentos 
WHERE proxima_data IS NOT NULL
GROUP BY DATE_FORMAT(proxima_data, '%Y-%m')
ORDER BY proxima_data ASC
LIMIT 6";
$resultado_por_mes = $conn->query($sql_por_mes);

// Configuração da Paginação
$limit = 10; // Registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$status = isset($_GET['status']) ? $_GET['status'] : '';
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$mes_filtro = isset($_GET['mes']) ? $_GET['mes'] : '';
$where_clauses = [];

if ($status) {
    $status_safe = $conn->real_escape_string($status);
    $where_clauses[] = "situacao = '$status_safe'";
}

if ($busca) {
    $busca_safe = $conn->real_escape_string($busca);
    $where_clauses[] = "(documento LIKE '%$busca_safe%' OR empresa LIKE '%$busca_safe%' OR responsavel LIKE '%$busca_safe%')";
}

if ($mes_filtro) {
    $mes_safe = $conn->real_escape_string($mes_filtro);
    $where_clauses[] = "DATE_FORMAT(proxima_data, '%Y-%m') = '$mes_safe'";
}

$where = '';
if (count($where_clauses) > 0) {
    $where = "WHERE " . implode(' AND ', $where_clauses);
}

// Contar total de registros com filtro
$count_sql = "SELECT COUNT(*) as total FROM vencimentos $where";
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT * FROM vencimentos $where ORDER BY validade ASC LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Vencimentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto mt-8 px-4">
        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="vencimentos.php" class="block bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Registros</h3>
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                        <i class="bi bi-folder2-open text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-gray-800"><?php echo $total_vencimentos; ?></div>
            </a>

            <a href="vencimentos.php?status=Vencido" class="block bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Vencidos</h3>
                    <div class="p-2 bg-red-50 rounded-lg text-red-600">
                        <i class="bi bi-exclamation-circle text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-gray-800"><?php echo $total_vencidos; ?></div>
            </a>

            <a href="vencimentos.php?status=Em dia" class="block bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Em Dia</h3>
                    <div class="p-2 bg-green-50 rounded-lg text-green-600">
                        <i class="bi bi-check-circle text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-gray-800"><?php echo $total_em_dia; ?></div>
            </a>

            <a href="vencimentos.php?status=Pendente" class="block bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Pendentes</h3>
                    <div class="p-2 bg-yellow-50 rounded-lg text-yellow-600">
                        <i class="bi bi-clock-history text-xl"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-gray-800"><?php echo $total_pendentes; ?></div>
            </a>
        </div>

        <!-- Card de Documentos por Mês (Próxima Data) -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="bi bi-calendar-event text-purple-600"></i>
                        Documentos por Mês (Próxima Data)
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Renovações agendadas agrupadas por mês</p>
                </div>
                <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                    <i class="bi bi-graph-up text-2xl"></i>
                </div>
            </div>

            <?php if ($resultado_por_mes && $resultado_por_mes->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php 
                    $meses_pt = [
                        'January' => 'Janeiro', 'February' => 'Fevereiro', 'March' => 'Março',
                        'April' => 'Abril', 'May' => 'Maio', 'June' => 'Junho',
                        'July' => 'Julho', 'August' => 'Agosto', 'September' => 'Setembro',
                        'October' => 'Outubro', 'November' => 'Novembro', 'December' => 'Dezembro'
                    ];
                    
                    while($mes_data = $resultado_por_mes->fetch_assoc()): 
                        $mes_formatado = $mes_data['mes'];
                        $data_obj = DateTime::createFromFormat('Y-m', $mes_formatado);
                        $mes_nome_en = $data_obj->format('F');
                        $ano = $data_obj->format('Y');
                        $mes_nome_pt = $meses_pt[$mes_nome_en];
                        $is_active = ($mes_filtro == $mes_formatado);
                    ?>
                        <a href="?mes=<?php echo $mes_formatado; ?>" class="block bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg p-4 border <?php echo $is_active ? 'border-purple-500 ring-2 ring-purple-300' : 'border-purple-100'; ?> hover:shadow-md transition-all hover:scale-105 cursor-pointer">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-calendar3 text-purple-600"></i>
                                    <span class="font-semibold text-gray-800"><?php echo $mes_nome_pt . ' ' . $ano; ?></span>
                                </div>
                                <span class="bg-purple-600 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                                    <?php echo $mes_data['total']; ?>
                                </span>
                            </div>
                            <p class="text-xs text-gray-600">
                                <?php echo $mes_data['total'] == 1 ? 'documento' : 'documentos'; ?> para renovar
                            </p>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="bi bi-calendar-x text-4xl mb-3 block text-gray-300"></i>
                    <p>Nenhum documento com próxima data agendada.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Controle de Vencimentos</h2>
                <p class="text-gray-500 text-sm">Gerencie todos os documentos e prazos</p>
                <?php if ($mes_filtro): 
                    $meses_pt = [
                        'January' => 'Janeiro', 'February' => 'Fevereiro', 'March' => 'Março',
                        'April' => 'Abril', 'May' => 'Maio', 'June' => 'Junho',
                        'July' => 'Julho', 'August' => 'Agosto', 'September' => 'Setembro',
                        'October' => 'Outubro', 'November' => 'Novembro', 'December' => 'Dezembro'
                    ];
                    $data_obj = DateTime::createFromFormat('Y-m', $mes_filtro);
                    $mes_nome = $meses_pt[$data_obj->format('F')] . ' de ' . $data_obj->format('Y');
                ?>
                    <div class="mt-2 inline-flex items-center gap-2 bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                        <i class="bi bi-funnel-fill"></i>
                        Renovações em <?php echo $mes_nome; ?>
                        <a href="vencimentos.php" class="ml-1 hover:text-purple-900" title="Remover filtro">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <form action="" method="GET" class="relative w-full md:w-64">
                    <?php if ($status): ?>
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
                    <?php endif; ?>
                    <?php if ($mes_filtro): ?>
                        <input type="hidden" name="mes" value="<?php echo htmlspecialchars($mes_filtro); ?>">
                    <?php endif; ?>
                    <input type="text" name="busca" value="<?php echo htmlspecialchars($busca); ?>" placeholder="Buscar..." class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <i class="bi bi-search"></i>
                    </div>
                    <?php if (!empty($busca) || !empty($mes_filtro)): ?>
                        <a href="vencimentos.php<?php echo $status ? '?status=' . urlencode($status) : ''; ?>" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600" title="Limpar filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                </form>
                <a href="adicionar_vencimento.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors shadow-sm whitespace-nowrap">
                    <i class="bi bi-plus-lg mr-2"></i> Novo
                </a>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data do Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsável</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Situação</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Validade</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $row['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo date('d/m/Y', strtotime($row['validade'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['documento']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['empresa']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['responsavel']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?php 
                                                echo match($row['situacao']) {
                                                    'Pendente' => 'bg-yellow-100 text-yellow-800',
                                                    'Em dia' => 'bg-green-100 text-green-800',
                                                    'Vencido' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            ?>">
                                                <?php echo htmlspecialchars($row['situacao']); ?>
                                            </span>
                                            <?php if ($row['situacao'] == 'Pendente' && !empty($row['observacao'])): ?>
                                                <span class="text-yellow-600 cursor-help" title="<?php echo htmlspecialchars($row['observacao']); ?>">
                                                    <i class="bi bi-info-circle-fill"></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $row['proxima_data'] ? date('d/m/Y', strtotime($row['proxima_data'])) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="editar_vencimento.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3 transition-colors" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="excluir_vencimento.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900 transition-colors" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este registro?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                    <i class="bi bi-inbox text-4xl mb-3 block text-gray-300"></i>
                                    Nenhum registro encontrado.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if ($total_pages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50">
                <span class="text-sm text-gray-500">
                    Mostrando <?php echo $start + 1; ?> a <?php echo min($start + $limit, $total_rows); ?> de <?php echo $total_rows; ?> resultados
                </span>
                <div class="flex gap-1">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($status); ?>&busca=<?php echo urlencode($busca); ?>&mes=<?php echo urlencode($mes_filtro); ?>" class="px-3 py-1 rounded border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status); ?>&busca=<?php echo urlencode($busca); ?>&mes=<?php echo urlencode($mes_filtro); ?>" class="px-3 py-1 rounded border <?php echo $i == $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'; ?> text-sm font-medium">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($status); ?>&busca=<?php echo urlencode($busca); ?>&mes=<?php echo urlencode($mes_filtro); ?>" class="px-3 py-1 rounded border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>