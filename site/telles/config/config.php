<?php
// Configurações do banco de dados
$servername = "127.0.0.1:3306";
$username = "u870367221_Telles";
$password = "Deividlps120@";
$dbname = "u870367221_Telles";

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

    // Identificar o dia da semana
    $dayOfWeek = date('N', strtotime($day)); // 1 (segunda-feira) a 7 (domingo)

    // Definir intervalos de trabalho baseados no dia da semana
    $timeIntervals = [];
    if ($dayOfWeek == 1) { // Segunda-feira
        $timeIntervals = [
            ['start' => "14:00", 'end' => "19:30"]
        ];
    } elseif ($dayOfWeek >= 2 && $dayOfWeek <= 4) { // Terça a quinta-feira
        $timeIntervals = [
            ['start' => "09:00", 'end' => "12:00"],
            ['start' => "14:00", 'end' => "19:30"]
        ];
    } elseif ($dayOfWeek == 5 || $dayOfWeek == 6) { // Sexta e sábado
        // Retornar mensagem especial para sexta e sábado
        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'Horários por ordem de chegada'
        ]);
        exit;
    } else {
        // Outros dias (domingo) sem horários disponíveis
        header('Content-Type: application/json');
        echo json_encode([
            'availableHours' => [],
            'unavailableHours' => []
        ]);
        exit;
    }

    // Gera todos os horários possíveis com intervalo de 30 minutos
    $allHours = [];
    foreach ($timeIntervals as $interval) {
        $startTime = strtotime($interval['start']);
        $endTime = strtotime($interval['end']);
        for ($time = $startTime; $time <= $endTime; $time += 1800) { // Intervalo de 30 minutos
            $allHours[] = date('H:i', $time);
        }
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
        $unavailableHours[] = date('H:i', $startTime);
    } else {
        // Adiciona o intervalo completo de horários entre `horario` e `horario_final`
        for ($time = $startTime; $time <= $endTime; $time += $timeInterval) {
            $unavailableHours[] = date('H:i', $time);
        }
    }
}

    // Calcular horários disponíveis
    $availableHours = array_values(array_diff($allHours, $unavailableHours));

    // Filtrar horários passados se o dia for hoje
    date_default_timezone_set('America/Sao_Paulo');
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i');

    if ($day === $currentDate) {
        $availableHours = array_filter($availableHours, function($hour) use ($currentTime) {
            return $hour >= $currentTime;
        });
        $availableHours = array_values($availableHours); // Reindexar o array
    }

    // Responder com horários disponíveis e indisponíveis
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
