<?php
require 'auth.php';
include 'config.php';

// Buscar especialidades para o filtro
$especialidades_result = $conn->query("SELECT DISTINCT especialidade FROM medicos WHERE especialidade IS NOT NULL AND especialidade != '' ORDER BY especialidade ASC");

// Configuração da Paginação
$limit = 10; // Registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$filtro_especialidade = isset($_GET['especialidade']) ? $_GET['especialidade'] : '';

$where_clauses = [];

if (!empty($busca)) {
    $busca_safe = $conn->real_escape_string($busca);
    $where_clauses[] = "(nome LIKE '%$busca_safe%' OR crm LIKE '%$busca_safe%' OR especialidade LIKE '%$busca_safe%' OR telefone LIKE '%$busca_safe%' OR email LIKE '%$busca_safe%')";
}

if (!empty($filtro_especialidade)) {
    $especialidade_safe = $conn->real_escape_string($filtro_especialidade);
    $where_clauses[] = "especialidade = '$especialidade_safe'";
}

$where = "";
if (count($where_clauses) > 0) {
    $where = "WHERE " . implode(' AND ', $where_clauses);
}

// Contar total de registros
$count_sql = "SELECT COUNT(*) as total FROM medicos $where";
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Buscar registros da página atual
$sql = "SELECT * FROM medicos $where ORDER BY nome ASC LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médicos</title>
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
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Médicos</h2>
                <p class="text-gray-500 text-sm">Gerencie o corpo clínico</p>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <form action="" method="GET" class="flex gap-2 w-full md:w-auto">
                    <select name="especialidade" onchange="this.form.submit()" class="w-full md:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm bg-white">
                        <option value="">Todas Especialidades</option>
                        <?php while($esp = $especialidades_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($esp['especialidade']); ?>" <?php echo $filtro_especialidade == $esp['especialidade'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($esp['especialidade']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <div class="relative w-full md:w-64">
                        <input type="text" name="busca" value="<?php echo htmlspecialchars($busca); ?>" placeholder="Buscar..." class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <i class="bi bi-search"></i>
                        </div>
                        <?php if (!empty($busca) || !empty($filtro_especialidade)): ?>
                            <a href="medicos.php" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600" title="Limpar filtros">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                <a href="adicionar_medico.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors shadow-sm whitespace-nowrap">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Médico
                </a>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CRM</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Setores</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">CNES</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">CREMESP</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $row['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['nome']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['crm']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['especialidade']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?php 
                                        if (!empty($row['setor'])) {
                                            $setores = json_decode($row['setor'], true);
                                            if (is_array($setores) && count($setores) > 0) {
                                                echo '<div class="flex flex-wrap gap-1">';
                                                foreach ($setores as $setor) {
                                                    $cor = match($setor) {
                                                        'Ambulatório' => 'bg-blue-100 text-blue-800',
                                                        'Centro Cirúrgico' => 'bg-purple-100 text-purple-800',
                                                        'UTI' => 'bg-red-100 text-red-800',
                                                        'Enfermaria' => 'bg-green-100 text-green-800',
                                                        'PA' => 'bg-yellow-100 text-yellow-800',
                                                        default => 'bg-gray-100 text-gray-800'
                                                    };
                                                    echo '<span class="px-2 py-0.5 rounded-full text-xs font-medium ' . $cor . '">' . htmlspecialchars($setor) . '</span>';
                                                }
                                                echo '</div>';
                                            } else {
                                                echo '<span class="text-gray-400 text-xs">-</span>';
                                            }
                                        } else {
                                            echo '<span class="text-gray-400 text-xs">-</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['telefone']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                        <input type="checkbox" disabled <?php echo $row['cnes'] ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                        <input type="checkbox" disabled <?php echo $row['cremesp'] ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="editar_medico.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3 transition-colors" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="excluir_medico.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900 transition-colors" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este médico?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-gray-500 text-sm">Nenhum médico cadastrado.</td>
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
                        <a href="?page=<?php echo $page - 1; ?>&busca=<?php echo urlencode($busca); ?>&especialidade=<?php echo urlencode($filtro_especialidade); ?>" class="px-3 py-1 rounded border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&busca=<?php echo urlencode($busca); ?>&especialidade=<?php echo urlencode($filtro_especialidade); ?>" class="px-3 py-1 rounded border <?php echo $i == $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'; ?> text-sm font-medium">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&busca=<?php echo urlencode($busca); ?>&especialidade=<?php echo urlencode($filtro_especialidade); ?>" class="px-3 py-1 rounded border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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