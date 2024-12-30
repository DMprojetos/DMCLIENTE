<?php
// Iniciar a sessão
session_start();

// Conectar ao banco de dados
$host = '127.0.0.1:3306'; // Altere conforme sua configuração
$db = 'u870367221_lordsveracruz'; // Nome do seu banco de dados
$user = 'u870367221_lordsveracruz'; // Usuário do banco de dados
$pass = 'Deividlps120@'; // Senha do banco de dados

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se o usuário está logado
if (!isset($_SESSION['telefone'])) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

// Obter o telefone do usuário da sessão
$telefoneUsuario = $_SESSION['telefone'];

// Buscar agendamentos na tabela `agendamentosbaiano` com base no telefone
$stmt = $conn->prepare("SELECT * FROM agendamentosbaiano WHERE telefone = ?");
$stmt->bind_param("s", $telefoneUsuario);
$stmt->execute();
$result = $stmt->get_result();

$agendamentos = [];
while ($row = $result->fetch_assoc()) {
    // Convertendo a data para o formato DD/MM/YYYY
    $dataFormatada = DateTime::createFromFormat('Y-m-d', $row['dia'])->format('d/m/Y');
    $agendamentos[] = [
        'id_agendamento' => $row['id_agendamento'],
        'dia' => $dataFormatada,
        'horario' => $row['horario'],
        'profissional' => $row['profissional'],
        'status' => $row['status'],
        'servico' => $row['servico']
    ];
}

$stmt->close();
$conn->close();

// Retornar os agendamentos em formato JSON
echo json_encode($agendamentos);
?>


