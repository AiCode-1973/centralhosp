<?php
include 'config.php';

// Aumentar o tamanho do campo telefone para suportar múltiplos números
$conn->query("ALTER TABLE medicos MODIFY COLUMN telefone VARCHAR(100)");

$data = <<<EOD
Abraão Teles Rocha	Cardiologista	
Adriana Brognara Martins	Dermatologista	(016) 99778-6562
Alfredo Petty Moutinho	Gastro 	99711-2570
Alice Homsi	Psicóloga	97411-2106
Aline Maynart Godoy	Fisiatra	997640849
Allison Zamara	Cirurgião Vascular	99799-7117
Andre Luis Andriolo	Neurologista	99764-7678
Andre Sementilli Cortina	Nefrologista/Clínico	(013)99612-1911
Angelica Croccia	Nutricionista	97401-0129
Cardec Batista F. Rufino	Cardiologista	(014) 99116-2342
Carlos Jose Galeazzi	Clinica Medica	(16) 997443956
Carmo Roberto F. Junior	Ortopedista	99777-9419
Daniel Reis A. Porto	Ginecologista	98840-4840
Elizabeth Cristiane C.Barroso Barreto	Psiquiatra	98123-0484
Erica Cecília A. de Gerard	Ortopedista 	99639-9261
Fabio Burin	Anestesista	99799-6515
Fabio Salles	Ortopedista	99797-1360
Fabio Tucci Longato	Urologista	(011) 99980-0410
Felipe G. Silva Souza	Cirurgião Cabeça e Pescoço	99777-8407
Fernando Sergio Ortiz Hazarian	Cardiologista	99740-7557
Gilmar Brancher	Anestesista	99741-9050
Giovanna Barcalla Silva	Cir.Cabeça e Pescoço	(011) 94145-6550
Gláucia Helena Lavorato	Dermatologista	98134-7375
Igor Marijuschkin	Ortopedista (pé)	99735-6655
João Luiz Cabral	Neuro-Cirurgião	99664-0666
João Paulo Pires Silveira	Hematologista	98143-9499
Joji Teruya	Cirurgião Geral	98216-5705
José Marcelo Garcia	Ginecologista	98112-3588
Laura Benhossi Floriano	Cardiologista	98112-4400
Lucas de Sena Lima	Proctologista	(013)99786-7273 / (011)99135-5032
Luis Augusto Dourado Lemos	URODINAMICA COMPLETA	99788-5606
Luis Eduardo Hichenbeck	Anestesista	99138-0146
Luiz Carlos Lopes Ferreira Junior	Ortopedista (joelho)	98189-0465
Luiz Claudio Behrmann Martins	Cardiologista	1397411-9905 // 133227-0001
Marcelo Spanó	Vascular Clínico	99782-6641
Maria Alice R. Souza	Ginecologista/Obstetricia	99767-4455
Mariana Rocha Bohne	Ginecologista	(091)8283-0203
Mauricio Menezes Vilela	Neurologista	(011)999-730473
Nathalie Cuconato de Almeida Costa	Nutricionista	99792-6271
Rachid Gorron Maloof	Cirurgião Plástico	(011)99774-3181
Raimundo Nogueira	Clinico Geral 	99742-2206
Ricardo Zecchetto S. Ramires	Mastologista	99600-0020
Rodrigo Gonçalves Silva	Gastro	99685-2626
Sergio Carneiro	Oncologista Cirúrgico	99672-4671
Sérgio Feijó Rodrigues (ver abaixo *)	Infectologista	99134-0906
Silvio R. Aquino Ayala	Oncologista	99103-6811
Tarcisio Adib	Urologista	99714-4467
Vanessa Munhoz Bitelman	Ginecologista	(011)98155-6065
Wagner Jose Riva	Cirurgião Geral	98131-0149
Waldemar Mathias Junior	Endocrinologista	99730-0309
Welber Erick Feitosa Meneses	Ortopedista	(088) 9620-6369
EOD;

$lines = explode("\n", trim($data));

foreach ($lines as $line) {
    // Tenta dividir por tabulação
    $parts = explode("\t", trim($line));
    
    $nome = isset($parts[0]) ? trim($parts[0]) : '';
    $especialidade = isset($parts[1]) ? trim($parts[1]) : '';
    $telefone = isset($parts[2]) ? trim($parts[2]) : '';

    if (empty($nome)) continue;

    $sql = "INSERT INTO medicos (nome, especialidade, telefone) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nome, $especialidade, $telefone);
    
    if ($stmt->execute()) {
        echo "Inserido: $nome <br>";
    } else {
        echo "Erro ao inserir $nome: " . $conn->error . "<br>";
    }
    $stmt->close();
}

echo "Importação de médicos concluída!";
$conn->close();
?>