<?php
require 'auth.php';
include 'config.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    $sql = "DELETE FROM medicos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: medicos.php");
    } else {
        echo "Erro ao excluir: " . $conn->error;
    }
    $stmt->close();
} else {
    header("Location: medicos.php");
}

$conn->close();
?>