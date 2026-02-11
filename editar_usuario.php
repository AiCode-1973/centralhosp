<?php
require 'auth.php';
include 'config.php';

if ($_SESSION['user_nivel'] != 'admin') {
    header("Location: index.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header("Location: usuarios.php");
    exit();
}

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $nivel = $_POST['nivel'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    $alterar_senha = !empty($_POST['senha']);
    
    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $check->bind_param("si", $email, $id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        $erro = "Este email já está cadastrado para outro usuário.";
    } else {
        if ($alterar_senha) {
            $senha = $_POST['senha'];
            $senha_confirma = $_POST['senha_confirma'];
            
            if ($senha != $senha_confirma) {
                $erro = "As senhas não coincidem.";
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nome=?, email=?, senha=?, nivel=?, ativo=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssii", $nome, $email, $senha_hash, $nivel, $ativo, $id);
            }
        } else {
            $sql = "UPDATE usuarios SET nome=?, email=?, nivel=?, ativo=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $nome, $email, $nivel, $ativo, $id);
        }
        
        if (!$erro && $stmt->execute()) {
            header("Location: usuarios.php");
            exit();
        } elseif (!$erro) {
            $erro = "Erro ao atualizar: " . $conn->error;
        }
        
        if (isset($stmt)) $stmt->close();
    }
    $check->close();
}

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    header("Location: usuarios.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
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
                <a href="usuarios.php" class="hover:text-blue-600"><i class="bi bi-arrow-left"></i> Voltar para lista</a>
            </div>

            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="text-lg font-semibold text-gray-800">Editar Usuário</h4>
                    <i class="bi bi-pencil-square text-gray-400 text-xl"></i>
                </div>
                
                <div class="p-6">
                    <?php if ($erro): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative mb-6 flex items-center gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span class="block sm:inline"><?php echo $erro; ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="space-y-6">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                            <input type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800 mb-3 font-medium">
                                <i class="bi bi-info-circle"></i> Alterar Senha (opcional)
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                                    <input type="password" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="senha" name="senha" placeholder="Deixe em branco para manter">
                                </div>
                                
                                <div>
                                    <label for="senha_confirma" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                                    <input type="password" class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" id="senha_confirma" name="senha_confirma" placeholder="Deixe em branco para manter">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="nivel" class="block text-sm font-medium text-gray-700 mb-1">Nível de Acesso</label>
                            <select class="w-full rounded-lg border-gray-300 border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow bg-white" id="nivel" name="nivel" required>
                                <option value="usuario" <?php echo $usuario['nivel'] == 'usuario' ? 'selected' : ''; ?>>Usuário</option>
                                <option value="admin" <?php echo $usuario['nivel'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="ativo" name="ativo" <?php echo $usuario['ativo'] ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="ativo" class="ml-2 block text-sm text-gray-900">Usuário ativo</label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <a href="usuarios.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors flex items-center gap-2">
                                <i class="bi bi-check-lg"></i> Salvar Alterações
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
