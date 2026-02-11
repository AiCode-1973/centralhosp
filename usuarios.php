<?php
require 'auth.php';
include 'config.php';

if ($_SESSION['user_nivel'] != 'admin') {
    header("Location: index.php");
    exit();
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

$where = '';
if ($busca) {
    $busca_safe = $conn->real_escape_string($busca);
    $where = "WHERE nome LIKE '%$busca_safe%' OR email LIKE '%$busca_safe%'";
}

$count_sql = "SELECT COUNT(*) as total FROM usuarios $where";
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT * FROM usuarios $where ORDER BY created_at DESC LIMIT $start, $limit";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Central Hosp</title>
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
                <h2 class="text-2xl font-bold text-gray-800">Usuários do Sistema</h2>
                <p class="text-gray-500 text-sm">Gerencie os acessos ao sistema</p>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <form action="" method="GET" class="relative w-full md:w-64">
                    <input type="text" name="busca" value="<?php echo htmlspecialchars($busca); ?>" placeholder="Buscar..." class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <i class="bi bi-search"></i>
                    </div>
                    <?php if (!empty($busca)): ?>
                        <a href="usuarios.php" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600" title="Limpar busca">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                </form>
                <a href="adicionar_usuario.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors shadow-sm whitespace-nowrap">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Usuário
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nível</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $row['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['nome']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $row['nivel'] == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                            <?php echo $row['nivel'] == 'admin' ? 'Administrador' : 'Usuário'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $row['ativo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $row['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="editar_usuario.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3 transition-colors" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                            <a href="excluir_usuario.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900 transition-colors" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    <i class="bi bi-inbox text-4xl mb-3 block text-gray-300"></i>
                                    Nenhum usuário encontrado.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50">
                <span class="text-sm text-gray-500">
                    Mostrando <?php echo $start + 1; ?> a <?php echo min($start + $limit, $total_rows); ?> de <?php echo $total_rows; ?> resultados
                </span>
                <div class="flex gap-1">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&busca=<?php echo urlencode($busca); ?>" class="px-3 py-1 rounded border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&busca=<?php echo urlencode($busca); ?>" class="px-3 py-1 rounded border <?php echo $i == $page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'; ?> text-sm font-medium">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&busca=<?php echo urlencode($busca); ?>" class="px-3 py-1 rounded border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
