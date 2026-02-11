<?php
include 'config.php';

$sql = "CREATE TABLE IF NOT EXISTS vencimentos (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    validade DATE NOT NULL,
    documento VARCHAR(255) NOT NULL,
    empresa VARCHAR(255) NOT NULL,
    responsavel VARCHAR(255) NOT NULL,
    situacao VARCHAR(50) NOT NULL,
    proxima_data DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabela 'vencimentos' criada com sucesso ou já existe.";
} else {
    echo "Erro ao criar tabela: " . $conn->error;
}

$conn->close();
?>