<?php
require 'auth.php';
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM vencimentos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: vencimentos.php");
    } else {
        echo "Erro ao excluir: " . $conn->error;
    }
    
    $stmt->close();
} else {
    header("Location: vencimentos.php");
}

$conn->close();
?>