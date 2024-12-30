<?php
// Inclua os arquivos do PHPMailer
require '/home/u870367221/domains/dmbarber.dmprojetos.com/public_html/login/config/src/Exception.php';
require '/home/u870367221/domains/dmbarber.dmprojetos.com/public_html/login/config/src/PHPMailer.php';
require '/home/u870367221/domains/dmbarber.dmprojetos.com/public_html/login/config/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Receba os dados do agendamento
$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$profissional = trim($_POST['profissional'] ?? '');
$servico = trim($_POST['servico'] ?? '');
$dia = trim($_POST['dia'] ?? '');
$horario = trim($_POST['horario'] ?? '');
$horario_final = trim($_POST['horario_final'] ?? '');
$tempo_total = trim($_POST['tempo_total'] ?? '');

if (empty($nome) || empty($telefone)) {
    echo "Erro: Nome e telefone são obrigatórios.";
    exit;
}

// Configurações do banco de dados
$servername = "127.0.0.1"; // Certifique-se de que o IP ou host está correto
$username = "u870367221_dmpainel";
$password = "Deividlps120@"; // Idealmente, use variáveis de ambiente para armazenar essa senha
$dbname = "u870367221_dmpainel";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

// Consultar o e-mail no banco de dados
$sql = "SELECT email FROM loginsite WHERE nome = ? AND telefone = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("ss", $nome, $telefone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $emailDestinatario = $row['email'];

    // Configuração do PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dmprojetos120@gmail.com';
        $mail->Password = 'Deividlps120@';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remetente e destinatário
        $mail->setFrom('dmprojetos120@gmail.com', 'DM Projetos');
        $mail->addAddress($emailDestinatario);

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = "Confirmacao de Agendamento";
        $mail->Body = "
    <h1>Detalhes do Agendamento</h1>
    <p><strong>Nome:</strong> $nome</p>
    <p><strong>Telefone:</strong> $telefone</p>
    <p><strong>Profissional:</strong> $profissional</p>
    <p><strong>Serviço:</strong> $servico</p>
    <p><strong>Dia:</strong> " . date('d/m/Y', strtotime($dia)) . "</p>
    <p><strong>Horário Inicial:</strong> $horario</p>
    <p><strong>Horário Final:</strong> $horario_final</p>
";


        // Enviar o e-mail
        $mail->send();
    } catch (Exception $e) {
        // Apenas registrar no log, sem mostrar ao usuário
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
    }
} else {
    // Registrar no log para análise posterior
    error_log("Erro: Não foi encontrado um usuário com nome '$nome' e telefone '$telefone'.");
}

// Não exibe mensagens ao usuário


// Fechar conexão com o banco de dados
$stmt->close();
$conn->close();
