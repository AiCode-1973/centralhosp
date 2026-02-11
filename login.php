<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include 'config.php';

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    $stmt = $conn->prepare("SELECT id, nome, email, senha, nivel, ativo FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        
        if ($usuario['ativo'] == 0) {
            $erro = "Usuário desativado. Entre em contato com o administrador.";
        } elseif (password_verify($senha, $usuario['senha'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_nome'] = $usuario['nome'];
            $_SESSION['user_email'] = $usuario['email'];
            $_SESSION['user_nivel'] = $usuario['nivel'];
            
            header("Location: index.php");
            exit();
        } else {
            $erro = "Email ou senha incorretos.";
        }
    } else {
        $erro = "Email ou senha incorretos.";
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Central Hosp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen flex items-center justify-center font-sans">
    <button onclick="toggleTheme()" class="fixed top-4 right-4 theme-toggle text-gray-600 hover:text-green-600 bg-white dark:bg-gray-800 px-4 py-2 rounded-lg shadow-lg z-50" title="Alternar Tema">
        <i id="theme-toggle-moon-icon" class="bi bi-moon-fill"></i>
        <i id="theme-toggle-sun-icon" class="bi bi-sun-fill hidden"></i>
    </button>
    <div class="w-full max-w-md px-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-700 px-8 py-10 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-4">
                    <i class="bi bi-hospital text-4xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Central Hosp</h1>
                <p class="text-green-100 text-sm">Sistema de Gestão Hospitalar</p>
            </div>

            <div class="px-8 py-10">
                <?php if ($erro): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span class="text-sm"><?php echo $erro; ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-envelope mr-1"></i> Email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required 
                            autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                            placeholder="seu@email.com"
                        >
                    </div>

                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-lock mr-1"></i> Senha
                        </label>
                        <input 
                            type="password" 
                            id="senha" 
                            name="senha" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                            placeholder="••••••••"
                        >
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors shadow-lg shadow-green-500/30 flex items-center justify-center gap-2"
                    >
                        <i class="bi bi-box-arrow-in-right"></i>
                        Entrar
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500">
                        <i class="bi bi-shield-lock mr-1"></i>
                        Área restrita - Acesso apenas para usuários autorizados
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <!--<p class="text-sm text-gray-600">
                Usuário padrão: <span class="font-mono bg-white px-2 py-1 rounded">admin@centralhosp.com</span><br>
                Senha padrão: <span class="font-mono bg-white px-2 py-1 rounded">admin123</span>
            </p>-->
        </div>
    </div>
    <script src="js/theme.js"></script>
</body>
</html>
