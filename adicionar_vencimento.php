<?php
require 'auth.php';
include 'config.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validade = $_POST['validade'];
    $documento = $_POST['documento'];
    $empresa = $_POST['empresa'];
    $responsavel = $_POST['responsavel'];
    $situacao = $_POST['situacao'];
    $proxima_data = !empty($_POST['proxima_data']) ? $_POST['proxima_data'] : NULL;
    $observacao = ($situacao == 'Pendente' && !empty($_POST['observacao'])) ? $_POST['observacao'] : NULL;

    $sql = "INSERT INTO vencimentos (validade, documento, empresa, responsavel, situacao, proxima_data, observacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $validade, $documento, $empresa, $responsavel, $situacao, $proxima_data, $observacao);

    if ($stmt->execute()) {
        header("Location: vencimentos.php");
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
    <title>Adicionar Vencimento</title>
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
                <a href="vencimentos.php" class="hover:text-blue-600"><i class="bi bi-arrow-left"></i> Voltar para lista</a>
            </div>

            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="text-lg font-semibold text-gray-800">Novo Vencimento</h4>
                    <i class="bi bi-file-earmark-plus text-gray-400 text-xl"></i>
                </div>
                
                <div class="p-6">
                    <?php if ($mensagem): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative mb-6 flex items-center gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span class="block sm:inline"><?php echo $mensagem; ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="validade" class="block text-sm font-medium text-gray-700 mb-1">Data do Documento</label>
                                <input type="date" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="validade" name="validade" required>
                            </div>
                            
                            <div>
                                <label for="situacao" class="block text-sm font-medium text-gray-700 mb-1">Situação</label>
                                <select class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow bg-white" id="situacao" name="situacao" required>
                                    <option value="Em dia">Em dia</option>
                                    <option value="Pendente">Pendente</option>
                                    <option value="Vencido">Vencido</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="documento" class="block text-sm font-medium text-gray-700 mb-1">Documento</label>
                            <input type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="documento" name="documento" placeholder="Ex: Alvará de Funcionamento" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="empresa" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                                <input type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="empresa" name="empresa" placeholder="Nome da empresa" required>
                            </div>
                            
                            <div>
                                <label for="responsavel" class="block text-sm font-medium text-gray-700 mb-1">Responsável</label>
                                <input type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="responsavel" name="responsavel" placeholder="Nome do responsável" required>
                            </div>
                        </div>

                        <div>
                            <label for="proxima_data" class="block text-sm font-medium text-gray-700 mb-1">Data de Validade</label>
                            <input type="date" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="proxima_data" name="proxima_data">
                            <p class="text-xs text-gray-500 mt-1">Data de vencimento do documento (opcional).</p>
                        </div>

                        <!-- Campo de Observação (visível apenas quando Pendente) -->
                        <div id="campo_observacao" style="display: none;">
                            <label for="observacao" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="bi bi-exclamation-triangle text-yellow-600"></i> Observação da Pendência
                            </label>
                            <textarea 
                                class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" 
                                id="observacao" 
                                name="observacao" 
                                rows="3"
                                placeholder="Descreva o motivo da pendência..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Informe o motivo ou detalhes da pendência.</p>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <a href="vencimentos.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors flex items-center gap-2">
                                <i class="bi bi-check-lg"></i> Salvar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Mostrar/ocultar campo de observação baseado na situação
        document.getElementById('situacao').addEventListener('change', function() {
            const campoObservacao = document.getElementById('campo_observacao');
            const observacaoTextarea = document.getElementById('observacao');
            
            if (this.value === 'Pendente') {
                campoObservacao.style.display = 'block';
                // Adiciona animação suave
                campoObservacao.style.opacity = '0';
                setTimeout(() => {
                    campoObservacao.style.transition = 'opacity 0.3s';
                    campoObservacao.style.opacity = '1';
                }, 10);
            } else {
                campoObservacao.style.display = 'none';
                observacaoTextarea.value = ''; // Limpa o campo se não for pendente
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>