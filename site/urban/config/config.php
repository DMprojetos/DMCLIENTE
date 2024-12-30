<?php
// Configurações do banco de dados
$servername = "127.0.0.1:3306";
$username = "u870367221_urban";
$password = "Deividlps120@";
$dbname = "u870367221_urban";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Processar a requisição de horários indisponíveis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['professional'], $_POST['day'])) {
    $professional = $_POST['professional'];
    $day = $_POST['day'];

    // Consulta para buscar horários agendados para o profissional no dia específico
    $stmt = $conn->prepare("SELECT horario, horario_final FROM agendamentosbaiano WHERE profissional = ? AND dia = ?");
    $stmt->bind_param("ss", $professional, $day);
    $stmt->execute();
    $result = $stmt->get_result();

    $unavailableHours = [];
    while ($row = $result->fetch_assoc()) {
        $startTime = strtotime($row['horario']);
        $endTime = strtotime($row['horario_final']);
        
        // Incrementa a cada 30 minutos, incluindo tanto o horário inicial quanto o horário final
        for ($time = $startTime; $time <= $endTime; $time += 1800) { 
            $unavailableHours[] = date('H:i', $time);
        }
    }

    header('Content-Type: application/json');
    echo json_encode($unavailableHours);

    $stmt->close();
    $conn->close();
    exit;
}

// Processar o agendamento com horário inicial e final
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'], $_POST['telefone'], $_POST['profissional'], $_POST['servico'], $_POST['dia'], $_POST['horario'], $_POST['horario_final'])) {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $profissional = $_POST['profissional'];
    $servico = $_POST['servico'];
    $dia = $_POST['dia'];
    $horario = date('H:i', strtotime($_POST['horario'])); // Horário inicial
    $horario_final = date('H:i', strtotime($_POST['horario_final'])); // Horário final

    // Verificar se todos os dados foram recebidos corretamente
    if (!$nome || !$telefone || !$profissional || !$servico || !$dia || !$horario || !$horario_final) {
        echo "Erro: Todos os campos são obrigatórios.";
        var_dump($_POST); // Exibe os dados recebidos para verificação
        exit;
    }

    // Tentar inserir o agendamento com um único registro, incluindo horário inicial e final
    $stmt = $conn->prepare("INSERT INTO agendamentosbaiano (nome, telefone, profissional, servico, dia, horario, horario_final) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $nome, $telefone, $profissional, $servico, $dia, $horario, $horario_final);

    if ($stmt->execute()) {
        echo "Agendamento realizado com sucesso!";
    } else {
        echo "Erro ao agendar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
