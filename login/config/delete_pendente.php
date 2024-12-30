<?php
// Definir o fuso horário para horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

// Configurações do banco de dados
$servername = "127.0.0.1:3306";
$username = "u870367221_dmpainel";
$password = "Deividlps120@";
$dbname = "u870367221_dmpainel";

// Função para excluir contas pendentes
function excluirContasPendentes($conn) {
    $sql = "DELETE FROM loginsite WHERE status = 'pendente' AND data_criacao < (NOW() - INTERVAL 5 MINUTE)";
    $conn->query($sql);
}

// Criar conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão e executar a função se a conexão for bem-sucedida
if (!$conn->connect_error) {
    excluirContasPendentes($conn);
}

// Fechar conexão
$conn->close();
?>
