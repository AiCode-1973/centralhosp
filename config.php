<?php
$host = '186.209.113.107';
$user = 'dema5738_centralhosp';
$pass = 'Dema@1973';
$dbname = 'dema5738_centralhosp';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Definir charset para utf8 para evitar problemas com acentuação
$conn->set_charset("utf8");
?>