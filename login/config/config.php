<?php

// Definir o fuso horário para horário de Brasília
date_default_timezone_set('America/Sao_Paulo');


// Configurações do banco de dados
$servername = "127.0.0.1:3306";
$username = "u870367221_dmpainel";
$password = "Deividlps120@";
$dbname = "u870367221_dmpainel";

// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se há erros na conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
} else {
    // echo "Conexão bem-sucedida!"; // Para debug
}

// Iniciar a sessão para gerenciar login
session_start();

// Verifica se a requisição é para cadastro
if (isset($_POST['acao']) && $_POST['acao'] === 'cadastro') {
    // Recebe e sanitiza os dados do formulário
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Verifica se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        echo "<script>alert('As senhas não coincidem!'); window.history.back();</script>";
        exit;
    }

    // Verificar se o email já está cadastrado
    $email_verificado = $conn->query("SELECT * FROM loginsite WHERE email='$email'");
    if ($email_verificado->num_rows > 0) {
        echo "<script>alert('Este email já está cadastrado!'); window.history.back();</script>";
        exit;
    }

    // Armazenar a senha com hash
    $senha_armazenada = password_hash($senha, PASSWORD_BCRYPT);

    // Preparar e executar a consulta SQL para cadastro
    $stmt = $conn->prepare("INSERT INTO loginsite (nome, email, senha) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $nome, $email, $senha_armazenada);
        if ($stmt->execute()) {
            echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href = 'index.html';</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar: " . $stmt->error . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Erro ao preparar a consulta: " . $conn->error . "'); window.history.back();</script>";
    }
    $stmt->close();
}

// Verifica se a requisição é para login
elseif (isset($_POST['acao']) && $_POST['acao'] === 'login') {
    // Recebe e sanitiza os dados do formulário
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $_POST['senha'];

    // Verificar se o usuário existe no banco de dados
    $sql = "SELECT * FROM loginsite WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verificar se a senha está correta
        if (password_verify($senha, $user['senha'])) {
            $_SESSION['usuario_nome'] = $user['nome'];
            echo "<script>alert('Login realizado com sucesso!'); window.location.href = 'agendamento.php';</script>";
            exit;
        } else {
            echo "<script>alert('Senha incorreta!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Email não encontrado!'); window.history.back();</script>";
    }
}

// Fechar a conexão
$conn->close();
?>
