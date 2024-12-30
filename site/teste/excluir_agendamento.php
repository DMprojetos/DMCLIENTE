<?php
// Ativar relatórios de erro
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar a sessão
session_start();
// Conar ao banco de dados
$host = '127.0.0.1:3306'; // Altere conforme sua configuração
$db = 'u870367221_teste'; // Nome do seu banco de dados
$user = 'u870367221_teste'; // Usuário do banco de dados
$pass = 'Deividlps120@'; // Senha do banco de dados

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    error_log("Falha na conexão: " . $conn->connect_error);
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se o usuário está logado
if (!isset($_SESSION['telefone'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

// Obter o ID do agendamento a ser cancelado
$id_agendamento = isset($_POST['id_agendamento']) ? intval($_POST['id_agendamento']) : 0;

// Verificar se o ID é válido
if ($id_agendamento > 0) {
    // Preparar a consulta para excluir o agendamento
    $stmt = $conn->prepare("DELETE FROM agendamentosbaiano WHERE id_agendamento = ?");
    if (!$stmt) {
        error_log("Erro na preparação da consulta: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Erro na preparação da consulta.']);
        exit();
    }
    $stmt->bind_param("i", $id_agendamento);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Agendamento cancelado com sucesso.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhum agendamento encontrado para cancelar.']);
        }
    } else {
        error_log("Erro ao cancelar o agendamento: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Erro ao cancelar o agendamento.']);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
}

$conn->close();
?>