<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <a class="text-2xl font-bold text-blue-600 flex items-center gap-2" href="index.php">
                <i class="bi bi-hospital"></i> Central Hosp
            </a>
            <div class="flex items-center gap-4">
                <a class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600'; ?> font-medium transition-colors" href="index.php">Dashboard</a>
                <a class="<?php echo basename($_SERVER['PHP_SELF']) == 'vencimentos.php' ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600'; ?> font-medium transition-colors" href="vencimentos.php">Vencimentos</a>
                <a class="<?php echo basename($_SERVER['PHP_SELF']) == 'medicos.php' ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600'; ?> font-medium transition-colors" href="medicos.php">Médicos</a>
                <?php if (isset($_SESSION['user_nivel']) && $_SESSION['user_nivel'] == 'admin'): ?>
                    <a class="<?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600'; ?> font-medium transition-colors" href="usuarios.php">Usuários</a>
                <?php endif; ?>
                <div class="flex items-center gap-3 ml-4 pl-4 border-l border-gray-300">
                    <button onclick="toggleTheme()" class="theme-toggle text-gray-600 hover:text-blue-600" title="Alternar Tema">
                        <i id="theme-toggle-moon-icon" class="bi bi-moon-fill"></i>
                        <i id="theme-toggle-sun-icon" class="bi bi-sun-fill hidden"></i>
                    </button>
                    <span class="text-sm text-gray-600">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_nome']); ?>
                    </span>
                    <a href="logout.php" class="text-red-600 hover:text-red-700 font-medium transition-colors" title="Sair">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
<script src="js/theme.js"></script>
