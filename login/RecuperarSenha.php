<?php
// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurações do banco de dados
$servername = "127.0.0.1:3306";
$username = "u870367221_dmpainel";
$password = "Deividlps120@";
$dbname = "u870367221_dmpainel";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o token foi passado pela URL
if (!isset($_GET['token'])) {
    die("Token de recuperação inválido.");
}

$token = $conn->real_escape_string($_GET['token']);

// Verificar se o token é válido e se não expirou
$query = $conn->prepare("SELECT email FROM recuperacao_senha WHERE token = ? AND expiracao > NOW()");
$query->bind_param("s", $token);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("Token inválido ou expirado.");
}

// Pegar o e-mail associado ao token
$row = $result->fetch_assoc();
$email = $row['email'];

// Processar o formulário de redefinição de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    // Verificar se as senhas coincidem
    if ($nova_senha !== $confirma_senha) {
        echo "<script>alert('As senhas não coincidem.'); window.history.back();</script>";
        exit;
    }

    // Criptografar a nova senha
    $senha_armazenada = password_hash($nova_senha, PASSWORD_DEFAULT);

    // Atualizar a senha no banco de dados
    $update = $conn->prepare("UPDATE loginsite SET senha = ? WHERE email = ?");
    $update->bind_param("ss", $senha_armazenada, $email);
    if ($update->execute()) {
        // Remover o token para que ele não possa ser reutilizado
        $delete = $conn->prepare("DELETE FROM recuperacao_senha WHERE token = ?");
        $delete->bind_param("s", $token);
        $delete->execute();

        echo "<script>alert('Senha redefinida com sucesso!'); window.location.href = 'index.html';</script>";
    } else {
        echo "<script>alert('Erro ao redefinir a senha.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Redefinir Senha</title>
</head>

<body>

    <div class="container">
        <form method="POST" action="">
            <div style="text-align: center;">
                <p style="font-size: 25px; color: #ffffff; margin: 0;">DM</p>
                <p style="font-size: 25px; color: #ffffff; margin: 0;">REDEFINIR SENHA</p>
            </div>

            <div class="input-container">
                <input id="nova_senha" name="nova_senha" placeholder="Digite sua nova senha" type="password" required>
                <a onclick="togglePass('nova_senha', 'eyeIcon1')" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); cursor: pointer; background-color: transparent; border: none;">
                    <i id="eyeIcon1" class="fas fa-eye-slash" style="color: #333;"></i>
                </a>
            </div>

            <div class="input-container">
                <input id="confirma_senha" name="confirma_senha" placeholder="Confirme sua nova senha" type="password" required>
                <a onclick="togglePass('confirma_senha', 'eyeIcon2')" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); cursor: pointer; background-color: transparent; border: none;">
                    <i id="eyeIcon2" class="fas fa-eye-slash" style="color: #333;"></i>
                </a>
            </div>

            <button type="submit" class="submi-button">Redefinir Senha</button>

            <div class="register-link">
                <p>Lembrou sua senha? <a href="index.html">Voltar ao login</a></p>
            </div>
        </form>
    </div>

    <script>
        function togglePass(inputId, iconId) {
            const senhaInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);

            if (senhaInput.type === "password") {
                senhaInput.type = "text";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            } else {
                senhaInput.type = "password";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            }
        }
    </script>

</body>

</html>
