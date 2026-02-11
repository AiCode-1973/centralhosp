<?php
include 'config.php';

$sql = "CREATE TABLE IF NOT EXISTS medicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    crm VARCHAR(20),
    especialidade VARCHAR(50),
    telefone VARCHAR(20),
    email VARCHAR(100)
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabela 'medicos' criada com sucesso!";
} else {
    echo "Erro ao criar tabela: " . $conn->error;
}

$conn->close();
?>