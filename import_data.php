<?php
include 'config.php';

$data = [
    ['11/1/2026', 'Desratização', 'HSE - Casa do Inseticida', 'Sr. Wanderley / Manutenção/ Patrimonio', '11/1/2026'],
    ['11/1/2026', 'Desinsetização', 'HSE - Casa do Inseticida', 'Sr. Wanderley / Manutenção/ Patrimonio', '11/1/2026'],
    ['8/1/2026', "Desinfecção/ Higienização da Caixa d'água", 'HSE - SOMAFILTROS', 'Sr. Wanderley / Manutenção/ Patrimonio', '11/1/2026'],
    ['19/05/2026', 'Licença Sanitária - Quimioterapia', 'HSE - ADM', 'ADM HSE', '19/05/2026'],
    ['22/04/2026', 'Extintores e Mangueiras', 'HSE - ML Extintores', 'Jurandir / SESMT', '22/04/2026'],
    ['Julho 2026', 'Certificado da Troca do Filtro Central', 'HSE - Central Filtros', 'Raul / Manutenção/ Patrimonio', '1/7/2026'],
    ['5/11/2025', 'Limpeza e Esgotamento Caixa de Gordura', 'HSE - HIDRO JATO LIMPADORA', 'Sr. Wanderley / Manutenção/ Patrimonio', '5/11/2025'],
    ['16/052026', 'Licença Sanitária - Farmácia', 'HSE - ADM', 'ADM HSE', '16/05/2026'],
    ['13/05/2025', 'Licença Sanitária - Hospital', 'HSE - ADM', 'ADM HSE', '8/8/2026'],
    ['3/12/2025', 'Higienização Bebedouros', 'HSE - SOMAFILTROS', 'Sr. Wanderley / Manutenção/ Patrimonio', '30/12/2025'],
    ['31/07/2026', 'ART TK Elevadores - (13)3003.0499', 'HSE - TK Elevadores', 'Sr. Wanderley / Manutenção/ Patrimonio', '31/07/2026'],
    ['30/06/2026', 'Certidão e Certificado de Regularidade CFM e CRM', 'HSE - ADM', 'ADM HSE', '2026'],
    ['30/07/2025', 'PGRSS', 'HSE - SCIH', 'SCIH', ''],
    ['7/5/2026', 'Licença Sanitária - Serviço de Radiologia', 'HSE - ADM', 'ADM HSE', '7/5/2026'],
    ['17/10/2026', 'Medição de Aterramento', 'HSE - T Alfaro Engenharia', 'Daniel / Manutenção/ Patrimonio', 'OUTUBRO / 2026'],
    ['1/10/2026', 'PGR', 'HSE - SESMT', 'SESMT / Jurandir', '7/18/1905'],
    ['2026', 'Eleição Diretoria Clinica', 'HSE - ADM', 'ADM HSE', '2026'],
    ['2026', 'Eleição Comissão de Ética Médica', 'HSE - ADM', 'ADM HSE', '2026'],
    ['20/08/2026', 'Brigada de Incencio - Manutenção/ Treinamento', 'HSE - SESMT', 'SESMT / Jurandir', '2026'],
    ['28/12/2025', 'AVCB', 'HSE - BOMBEIROS', 'ADM / HSE', '28/12/2025'],
    ['28/12/2025', 'Alvará de Funcionamento Estacionamento', 'HSE - ADM', 'ADM HSE', '28/12/2025'],
    ['28/12/2025', 'Alvará de Funcionamento Hospital', 'HSE - ADM', 'ADM HSE', '28/12/2025']
];

function parseDate($dateStr) {
    if (empty($dateStr)) return null;
    
    $dateStr = trim($dateStr);
    
    // Fix specific typo
    if ($dateStr == '16/052026') return '2026-05-16';
    
    // Handle "Month Year" or "Month / Year"
    $months = [
        'janeiro' => '01', 'fevereiro' => '02', 'março' => '03', 'abril' => '04',
        'maio' => '05', 'junho' => '06', 'julho' => '07', 'agosto' => '08',
        'setembro' => '09', 'outubro' => '10', 'novembro' => '11', 'dezembro' => '12'
    ];
    
    foreach ($months as $name => $num) {
        if (stripos($dateStr, $name) !== false) {
            if (preg_match('/(\d{4})/', $dateStr, $matches)) {
                return $matches[1] . '-' . $num . '-01';
            }
        }
    }
    
    // Handle just Year "2026"
    if (preg_match('/^\d{4}$/', $dateStr)) {
        return $dateStr . '-01-01';
    }
    
    // Handle d/m/Y
    $parts = explode('/', $dateStr);
    if (count($parts) == 3) {
        // Handle m/d/Y vs d/m/Y ambiguity? Assuming d/m/Y based on context (Brazil)
        // But wait, 7/18/1905 -> 18 is month? No, 18 is day. So 7 is month? Or 7 is day?
        // Standard PT-BR is d/m/Y.
        // 7/18/1905 -> Month 18 impossible. So it must be m/d/Y? Or typo?
        // Let's assume d/m/Y unless invalid.
        
        $d = $parts[0];
        $m = $parts[1];
        $y = $parts[2];
        
        if ($m > 12) { // Swap if month > 12
            $temp = $d;
            $d = $m;
            $m = $temp;
        }
        
        return sprintf('%04d-%02d-%02d', $y, $m, $d);
    }
    
    return null;
}

$stmt = $conn->prepare("INSERT INTO vencimentos (validade, documento, empresa, responsavel, situacao, proxima_data) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($data as $row) {
    $validadeRaw = $row[0];
    $documento = $row[1];
    $empresa = $row[2];
    $responsavel = $row[3];
    $proximaDataRaw = $row[4];
    
    $validade = parseDate($validadeRaw);
    $proxima_data = parseDate($proximaDataRaw);
    
    // Calculate situation
    $situacao = 'Em dia';
    if ($validade) {
        $today = date('Y-m-d');
        if ($validade < $today) {
            $situacao = 'Vencido';
        }
    } else {
        $situacao = 'Pendente'; // No date?
    }
    
    $stmt->bind_param("ssssss", $validade, $documento, $empresa, $responsavel, $situacao, $proxima_data);
    
    if ($stmt->execute()) {
        echo "Inserido: $documento <br>";
    } else {
        echo "Erro ao inserir $documento: " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();
echo "Importação concluída!";
?>