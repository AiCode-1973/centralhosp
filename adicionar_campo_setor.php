<?php
include 'config.php';

// Adicionar coluna setor na tabela medicos
$sql = "ALTER TABLE medicos ADD COLUMN setor TEXT NULL AFTER especialidade";

if ($conn->query($sql) === TRUE) {
    echo "Coluna 'setor' adicionada com sucesso!";
} else {
    // Se der erro, pode ser porque já existe, vamos verificar
    $check = $conn->query("SHOW COLUMNS FROM medicos LIKE 'setor'");
    if ($check->num_rows > 0) {
        echo "Coluna 'setor' já existe!";
    } else {
        echo "Erro: " . $conn->error;
    }
}

$conn->close();
?>
