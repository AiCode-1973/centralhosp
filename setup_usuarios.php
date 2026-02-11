<?php
include 'config.php';

$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nivel VARCHAR(20) DEFAULT 'usuario',
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabela 'usuarios' criada com sucesso!<br>";
    
    // Criar usuário admin padrão (senha: admin123)
    $nome_admin = "Administrador";
    $email_admin = "admin@centralhosp.com";
    $senha_admin = password_hash("admin123", PASSWORD_DEFAULT);
    $nivel_admin = "admin";
    
    $check = $conn->query("SELECT id FROM usuarios WHERE email = '$email_admin'");
    
    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome_admin, $email_admin, $senha_admin, $nivel_admin);
        
        if ($stmt->execute()) {
            echo "Usuário admin criado com sucesso!<br>";
            echo "Email: admin@centralhosp.com<br>";
            echo "Senha: admin123<br>";
        }
        $stmt->close();
    }
} else {
    echo "Erro ao criar tabela: " . $conn->error;
}

$conn->close();
?>
