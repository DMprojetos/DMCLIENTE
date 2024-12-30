<?php

// Definir o fuso horário para horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

// Configurações do banco de dados
$servername = "127.0.0.1:3306";
$username = "u870367221_dmpainel";
$password = "Deividlps120@";
$dbname = "u870367221_dmpainel";

// Criar a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Verificar se o token está presente na URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Buscar o usuário com o token fornecido e status 'pendente'
    $stmt = $conn->prepare("SELECT * FROM loginsite WHERE token = ? AND status = 'pendente'");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token válido, ativar a conta
        $stmt_update = $conn->prepare("UPDATE loginsite SET status = 'ativo', token = NULL WHERE token = ?");
        $stmt_update->bind_param("s", $token);
        
        if ($stmt_update->execute()) {
            echo "<script>alert('Conta ativada com sucesso! Você já pode fazer login.'); window.location.href = 'https://dmbarber.dmprojetos.com/login/';</script>";
        } else {
            echo "Erro ao ativar a conta: " . $conn->error;
        }

        $stmt_update->close();
    } else {
        echo "Token inválido ou conta já ativada.";
    }

    $stmt->close();
} else {
    echo "Token não fornecido.";
}

// Fechar a conexão
$conn->close();
?>
