<?php
// Configurações do banco de dados
$servername = "127.0.0.1:3306";
$username = "u870367221_BaianoduCorte";
$password = "Deividlps120@";
$dbname = "u870367221_BaianoduCorte";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Processar a requisição de horários disponíveis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['professional'], $_POST['day'])) {
    $professional = $_POST['professional'];
    $day = $_POST['day'];

    // Verificar se o dia é 25/11/2024 e o profissional é Baiano ou Gabriel
    if ($day === '2024-11-25' && in_array($professional, ['Baiano', 'Gabriel'])) {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Horários apenas com Guilherme para o dia 25/11/2024.']);
        exit; // Impede que os horários sejam gerados
    }

    // Identificar o dia da semana
    $dayOfWeek = date('N', strtotime($day)); // 1 (segunda-feira) a 7 (domingo)

    // Verificar se o mês é dezembro
    if (date('m', strtotime($day)) === '12') {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Horário por ordem de chegada para o mês de dezembro.']);
        exit; // Impede que os horários sejam gerados
    }

    // Verificar se é 20/11/2024
    if ($day === '2024-11-20') {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Horários por ordem de chegada - Data especial!']);
        exit;
    }

    // Verificar se é sábado (6) ou outro dia com horários normais
    if ($dayOfWeek == 6) { // Sábado
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Horários por ordem de chegada']);
        exit;
    }

    // Definir os intervalos de trabalho
    $morningStart = strtotime("09:00");
    $morningEnd = strtotime("11:00");
    $afternoonStart = strtotime("14:00");
    $afternoonEnd = strtotime("19:00");
    $timeInterval = 1800; // 30 minutos

    // Gera todos os horários possíveis nos intervalos de trabalho
    $allHours = [];
    for ($time = $morningStart; $time <= $morningEnd; $time += $timeInterval) {
        $allHours[] = date('H:i:s', $time);
    }
    for ($time = $afternoonStart; $time <= $afternoonEnd; $time += $timeInterval) {
        $allHours[] = date('H:i:s', $time);
    }

    // Consulta para buscar horários agendados para o profissional no dia específico
    $stmt = $conn->prepare("SELECT horario, horario_final FROM agendamentosbaiano WHERE profissional = ? AND dia = ?");
    $stmt->bind_param("ss", $professional, $day);
    $stmt->execute();
    $result = $stmt->get_result();

    $unavailableHours = [];
    while ($row = $result->fetch_assoc()) {
        $startTime = strtotime($row['horario']);
        $endTime = strtotime($row['horario_final']);

        // Se `horario_final` for `00:00:00`, apenas o horário inicial é considerado indisponível
        if ($endTime == strtotime("00:00:00")) {
            $unavailableHours[] = date('H:i:s', $startTime);
        } else {
            // Adicionar horários entre `horario` e `horario_final`, incluindo o horário final
            for ($time = $startTime; $time < $endTime; $time += $timeInterval) {
                $unavailableHours[] = date('H:i:s', $time);
            }
            // Adiciona o horário final explicitamente
            $unavailableHours[] = date('H:i:s', $endTime);
        }
    }

    // Calcular a diferença entre todos os horários e os horários indisponíveis
    $availableHours = array_values(array_diff($allHours, $unavailableHours));

    // Obter o horário atual em Brasília
    date_default_timezone_set('America/Sao_Paulo');
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i');

    // Se o dia selecionado for hoje, filtrar os horários disponíveis para excluir horários que já passaram
    if ($day === $currentDate) {
        $availableHours = array_filter($availableHours, function ($hour) use ($currentTime) {
            return $hour >= $currentTime;
        });
        $availableHours = array_values($availableHours); // Reindexar o array
    }

    // Enviar tanto os horários disponíveis quanto os indisponíveis para o frontend
    header('Content-Type: application/json');
    echo json_encode([
        'availableHours' => $availableHours,
        'unavailableHours' => array_values($unavailableHours),
    ]);

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

        // Insere o nome, telefone e último agendamento na tabela 'clientes'
        $stmt_clientes = $conn->prepare("INSERT INTO clientes (nome, telefone, ultimo_agendamento) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE ultimo_agendamento = VALUES(ultimo_agendamento)");
        $stmt_clientes->bind_param("sss", $nome, $telefone, $dia);

        if ($stmt_clientes->execute()) {
            echo "Informações do cliente atualizadas com sucesso!";
        } else {
            echo "Erro ao atualizar cliente: " . $stmt_clientes->error;
        }

        $stmt_clientes->close();
    } else {
        echo "Erro ao agendar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
