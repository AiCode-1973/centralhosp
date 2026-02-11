<?php
require 'auth.php';
include 'config.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $crm = $_POST['crm'];
    $especialidade = $_POST['especialidade'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $cnes = isset($_POST['cnes']) ? 1 : 0;
    $cremesp = isset($_POST['cremesp']) ? 1 : 0;
    
    // Processar setores selecionados
    $setores = isset($_POST['setor']) ? json_encode($_POST['setor']) : NULL;

    $sql = "INSERT INTO medicos (nome, crm, especialidade, setor, telefone, email, cnes, cremesp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssii", $nome, $crm, $especialidade, $setores, $telefone, $email, $cnes, $cremesp);

    if ($stmt->execute()) {
        header("Location: medicos.php");
        exit();
    } else {
        $mensagem = "Erro ao cadastrar: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Médico</title>
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
        <div class="max-w-2xl mx-auto">
            <div class="mb-6 flex items-center gap-2 text-sm text-gray-500">
                <a href="medicos.php" class="hover:text-blue-600"><i class="bi bi-arrow-left"></i> Voltar para lista</a>
            </div>

            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="text-lg font-semibold text-gray-800">Novo Médico</h4>
                    <i class="bi bi-person-plus text-gray-400 text-xl"></i>
                </div>
                
                <div class="p-6">
                    <?php if ($mensagem): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative mb-6 flex items-center gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span class="block sm:inline"><?php echo $mensagem; ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="space-y-6">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                            <input type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="nome" name="nome" placeholder="Dr. João Silva" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="crm" class="block text-sm font-medium text-gray-700 mb-1">CRM</label>
                                <input type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="crm" name="crm" placeholder="12345/SP" required>
                            </div>
                            
                            <div>
                                <label for="especialidade" class="block text-sm font-medium text-gray-700 mb-1">Especialidade</label>
                                <input type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="especialidade" name="especialidade" placeholder="Cardiologia" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="telefone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                <input type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="telefone" name="telefone" placeholder="(11) 99999-9999" required>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="email" name="email" placeholder="medico@exemplo.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="bi bi-hospital"></i> Setores de Atuação (selecione um ou mais)
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center">
                                    <input type="checkbox" id="setor_ambulatorio" name="setor[]" value="Ambulatório" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="setor_ambulatorio" class="ml-2 block text-sm text-gray-900">Ambulatório</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="setor_centro_cirurgico" name="setor[]" value="Centro Cirúrgico" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="setor_centro_cirurgico" class="ml-2 block text-sm text-gray-900">Centro Cirúrgico</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="setor_uti" name="setor[]" value="UTI" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="setor_uti" class="ml-2 block text-sm text-gray-900">UTI</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="setor_enfermaria" name="setor[]" value="Enfermaria" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="setor_enfermaria" class="ml-2 block text-sm text-gray-900">Enfermaria</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="setor_pa" name="setor[]" value="PA" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="setor_pa" class="ml-2 block text-sm text-gray-900">PA</label>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-6 pt-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="cnes" name="cnes" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="cnes" class="ml-2 block text-sm text-gray-900">Possui CNES</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="cremesp" name="cremesp" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="cremesp" class="ml-2 block text-sm text-gray-900">Possui CREMESP</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <a href="medicos.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors flex items-center gap-2">
                                <i class="bi bi-check-lg"></i> Salvar Médico
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>