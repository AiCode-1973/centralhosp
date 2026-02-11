<?php
include 'config.php';

// Adicionar coluna observacao na tabela vencimentos
$sql = "ALTER TABLE vencimentos ADD COLUMN IF NOT EXISTS observacao TEXT NULL AFTER proxima_data";

if ($conn->query($sql) === TRUE) {
    echo "Coluna 'observacao' adicionada com sucesso!";
} else {
    // Se der erro, pode ser porque já existe, vamos verificar
    $check = $conn->query("SHOW COLUMNS FROM vencimentos LIKE 'observacao'");
    if ($check->num_rows > 0) {
        echo "Coluna 'observacao' já existe!";
    } else {
        echo "Erro: " . $conn->error;
    }
}

$conn->close();
?>
