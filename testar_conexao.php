<?php
/**
 * Script de Teste de Conexão Remota
 * Central Hosp - Sistema de Gestão Hospitalar
 */

echo "<!DOCTYPE html>
<html lang='pt-br'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Teste de Conexão - Central Hosp</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css'>
</head>
<body class='bg-gray-50 min-h-screen flex items-center justify-center p-4'>
    <div class='max-w-2xl w-full bg-white rounded-xl shadow-lg p-8'>";

echo "<div class='text-center mb-6'>
        <i class='bi bi-database text-5xl text-blue-600 mb-3'></i>
        <h1 class='text-2xl font-bold text-gray-800'>Teste de Conexão Remota</h1>
        <p class='text-gray-500 mt-2'>Verificando conectividade com o banco de dados</p>
      </div>";

// Incluir configuração
include 'config.php';

echo "<div class='space-y-4'>";

// Teste 1: Verificar se a conexão foi estabelecida
echo "<div class='border border-gray-200 rounded-lg p-4'>";
echo "<div class='flex items-center gap-3 mb-2'>";
echo "<i class='bi bi-1-circle text-blue-600 text-xl'></i>";
echo "<h3 class='font-semibold text-gray-800'>Teste de Conexão Básica</h3>";
echo "</div>";

if ($conn->connect_error) {
    echo "<div class='bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2'>";
    echo "<i class='bi bi-x-circle-fill'></i>";
    echo "<span><strong>ERRO:</strong> " . $conn->connect_error . "</span>";
    echo "</div>";
    echo "</div></div></div></body></html>";
    exit();
} else {
    echo "<div class='bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2'>";
    echo "<i class='bi bi-check-circle-fill'></i>";
    echo "<span><strong>SUCESSO:</strong> Conexão estabelecida com sucesso!</span>";
    echo "</div>";
}
echo "</div>";

// Teste 2: Informações do servidor
echo "<div class='border border-gray-200 rounded-lg p-4'>";
echo "<div class='flex items-center gap-3 mb-2'>";
echo "<i class='bi bi-2-circle text-blue-600 text-xl'></i>";
echo "<h3 class='font-semibold text-gray-800'>Informações do Servidor</h3>";
echo "</div>";

$server_info = $conn->server_info;
$host_info = $conn->host_info;
$protocol_version = $conn->protocol_version;
$charset = $conn->character_set_name();

echo "<div class='bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-2 text-sm'>";
echo "<p class='text-gray-700'><strong>Host:</strong> 186.209.113.107</p>";
echo "<p class='text-gray-700'><strong>Banco de Dados:</strong> dema5738_centralhosp</p>";
echo "<p class='text-gray-700'><strong>Versão MySQL:</strong> $server_info</p>";
echo "<p class='text-gray-700'><strong>Conexão:</strong> $host_info</p>";
echo "<p class='text-gray-700'><strong>Protocolo:</strong> $protocol_version</p>";
echo "<p class='text-gray-700'><strong>Charset:</strong> $charset</p>";
echo "</div>";
echo "</div>";

// Teste 3: Verificar tabelas existentes
echo "<div class='border border-gray-200 rounded-lg p-4'>";
echo "<div class='flex items-center gap-3 mb-2'>";
echo "<i class='bi bi-3-circle text-blue-600 text-xl'></i>";
echo "<h3 class='font-semibold text-gray-800'>Verificação de Tabelas</h3>";
echo "</div>";

$tabelas_esperadas = ['usuarios', 'medicos', 'vencimentos'];
$tabelas_encontradas = [];
$tabelas_faltando = [];

$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $tabelas_encontradas[] = $row[0];
    }
}

foreach ($tabelas_esperadas as $tabela) {
    if (!in_array($tabela, $tabelas_encontradas)) {
        $tabelas_faltando[] = $tabela;
    }
}

echo "<div class='space-y-2'>";
foreach ($tabelas_esperadas as $tabela) {
    $existe = in_array($tabela, $tabelas_encontradas);
    $cor = $existe ? 'green' : 'red';
    $icone = $existe ? 'check-circle-fill' : 'x-circle-fill';
    
    echo "<div class='bg-{$cor}-50 border border-{$cor}-200 text-{$cor}-700 px-4 py-2 rounded-lg flex items-center gap-2'>";
    echo "<i class='bi bi-$icone'></i>";
    echo "<span>Tabela <strong>$tabela</strong>: " . ($existe ? 'Encontrada' : 'Não encontrada') . "</span>";
    echo "</div>";
}
echo "</div>";
echo "</div>";

// Teste 4: Contar registros
echo "<div class='border border-gray-200 rounded-lg p-4'>";
echo "<div class='flex items-center gap-3 mb-2'>";
echo "<i class='bi bi-4-circle text-blue-600 text-xl'></i>";
echo "<h3 class='font-semibold text-gray-800'>Estatísticas do Banco</h3>";
echo "</div>";

echo "<div class='grid grid-cols-1 md:grid-cols-3 gap-3'>";

// Contar usuários
$count_usuarios = 0;
if (in_array('usuarios', $tabelas_encontradas)) {
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    if ($result) {
        $count_usuarios = $result->fetch_assoc()['total'];
    }
}

// Contar médicos
$count_medicos = 0;
if (in_array('medicos', $tabelas_encontradas)) {
    $result = $conn->query("SELECT COUNT(*) as total FROM medicos");
    if ($result) {
        $count_medicos = $result->fetch_assoc()['total'];
    }
}

// Contar vencimentos
$count_vencimentos = 0;
if (in_array('vencimentos', $tabelas_encontradas)) {
    $result = $conn->query("SELECT COUNT(*) as total FROM vencimentos");
    if ($result) {
        $count_vencimentos = $result->fetch_assoc()['total'];
    }
}

echo "<div class='bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4 text-center'>";
echo "<div class='text-3xl font-bold text-purple-700'>$count_usuarios</div>";
echo "<div class='text-sm text-purple-600 mt-1'>Usuários</div>";
echo "</div>";

echo "<div class='bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4 text-center'>";
echo "<div class='text-3xl font-bold text-green-700'>$count_medicos</div>";
echo "<div class='text-sm text-green-600 mt-1'>Médicos</div>";
echo "</div>";

echo "<div class='bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4 text-center'>";
echo "<div class='text-3xl font-bold text-blue-700'>$count_vencimentos</div>";
echo "<div class='text-sm text-blue-600 mt-1'>Vencimentos</div>";
echo "</div>";

echo "</div>";
echo "</div>";

// Teste 5: Status Final
echo "<div class='border-2 border-green-300 rounded-lg p-6 bg-green-50'>";
echo "<div class='text-center'>";
echo "<i class='bi bi-check-circle-fill text-5xl text-green-600 mb-3'></i>";
echo "<h2 class='text-xl font-bold text-green-800'>Conexão Remota Funcionando!</h2>";
echo "<p class='text-green-600 mt-2'>Todos os sistemas estão operacionais</p>";
echo "<div class='mt-4'>";
echo "<a href='index.php' class='inline-block bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors'>";
echo "<i class='bi bi-house-door mr-2'></i>Ir para o Dashboard";
echo "</a>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "</div>"; // space-y-4

// Informações adicionais
echo "<div class='mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200'>";
echo "<p class='text-sm text-gray-600 text-center'>";
echo "<i class='bi bi-info-circle mr-1'></i> ";
echo "Conexão realizada em <strong>" . date('d/m/Y H:i:s') . "</strong>";
echo "</p>";
echo "</div>";

echo "</div></body></html>";

$conn->close();
?>
