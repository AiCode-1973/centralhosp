<?php
require 'auth.php';
include 'config.php';

// Consultas para estatísticas
$total_medicos = $conn->query("SELECT COUNT(*) as count FROM medicos")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Hosp</title>
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
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-gray-600 mt-1">Visão geral dos módulos do sistema</p>
        </div>

        <!-- Ações Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="vencimentos.php" class="group block p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:border-blue-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-full group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="bi bi-list-ul text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">Gerenciar Vencimentos</h3>
                        <p class="text-gray-500 text-sm mt-1">Acessar a lista completa, editar e excluir registros.</p>
                    </div>
                    <div class="ml-auto text-gray-400 group-hover:text-blue-500">
                        <i class="bi bi-arrow-right text-xl"></i>
                    </div>
                </div>
            </a>

            <a href="medicos.php" class="group block p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:border-blue-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-100 text-green-600 rounded-full group-hover:bg-green-600 group-hover:text-white transition-colors">
                        <i class="bi bi-people text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">Gerenciar Médicos</h3>
                        <p class="text-gray-500 text-sm mt-1">Cadastrar e gerenciar o corpo clínico (<?php echo $total_medicos; ?> cadastrados).</p>
                    </div>
                    <div class="ml-auto text-gray-400 group-hover:text-blue-500">
                        <i class="bi bi-arrow-right text-xl"></i>
                    </div>
                </div>
            </a>

            <!--<a href="adicionar_vencimento.php" class="group block p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:border-green-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-100 text-green-600 rounded-full group-hover:bg-green-600 group-hover:text-white transition-colors">
                        <i class="bi bi-plus-lg text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600">Novo Cadastro</h3>
                        <p class="text-gray-500 text-sm mt-1">Adicionar um novo documento ou vencimento ao sistema.</p>
                    </div>
                    <div class="ml-auto text-gray-400 group-hover:text-green-500">
                        <i class="bi bi-arrow-right text-xl"></i>
                    </div>
                </div>
            </a>-->
        </div>
        
        
    </div>
</body>
</html>