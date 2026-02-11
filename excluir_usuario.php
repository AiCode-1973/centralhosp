<?php
require 'auth.php';
include 'config.php';

if ($_SESSION['user_nivel'] != 'admin') {
    header("Location: index.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id && $id != $_SESSION['user_id']) {
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: usuarios.php");
    } else {
        echo "Erro ao excluir: " . $conn->error;
    }
    $stmt->close();
} else {
    header("Location: usuarios.php");
}

$conn->close();
?>
