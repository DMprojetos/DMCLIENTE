<?php

// Definir o fuso horário para horário de Brasília
date_default_timezone_set('America/Sao_Paulo');

// Inclua manualmente os arquivos do PHPMailer
require '/home/u870367221/domains/dmbarber.dmprojetos.com/public_html/login/config/src/Exception.php';
require '/home/u870367221/domains/dmbarber.dmprojetos.com/public_html/login/config/src/PHPMailer.php';
require '/home/u870367221/domains/dmbarber.dmprojetos.com/public_html/login/config/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Habilitar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt'); // Definir o arquivo de log

// Configurações do banco de dados
$servername = "127.0.0.1:3306";
$username = "u870367221_dmpainel";
$password = "Deividlps120@";
$dbname = "u870367221_dmpainel";

// Criar a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    error_log("Erro na conexão com o banco de dados: " . $conn->connect_error);
    echo "<html><head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          </head><body>
          <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Conexão falhou: " . addslashes($conn->connect_error) . "',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.back();
            });
          </script>
          </body></html>";
    exit;
}

// Receber e sanitizar os dados do formulário
$nome = $conn->real_escape_string($_POST['nome'] ?? '');
$email = $conn->real_escape_string($_POST['email'] ?? '');
$telefone = $conn->real_escape_string($_POST['telefone'] ?? '');
$telefone = preg_replace('/\D/', '', $telefone); // Remover todos os caracteres não numéricos do telefone
$senha = $conn->real_escape_string($_POST['senha'] ?? '');

// Verificar se o telefone já está cadastrado
$telefone_verificado = $conn->query("SELECT * FROM loginsite WHERE telefone='$telefone'");
if ($telefone_verificado && $telefone_verificado->num_rows > 0) {
    echo "<html><head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      </head><body>
      <script>
        Swal.fire({
            icon: 'warning',
            title: 'Telefone já cadastrado!',
            text: 'Por favor, use um número de telefone diferente.',
            confirmButtonText: 'OK',
            width: '400px',
            padding: '1.5rem',
            customClass: {
                popup: 'swal-wide'
            }
        }).then(() => {
            window.history.back();
        });
      </script>
      <style>
          .swal-wide { width: 400px !important; }
      </style>
      </body></html>";
    exit;
}

// Verificar se as senhas coincidem no servidor
if (isset($_POST['senha']) && isset($_POST['confirmar_senha'])) {
    $senha = $conn->real_escape_string($_POST['senha']);
    $confirmar_senha = $conn->real_escape_string($_POST['confirmar_senha']);
    
    if ($senha !== $confirmar_senha) {
        echo "<html><head>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              </head><body>
              <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'As senhas não coincidem!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.history.back();
                });
              </script>
              </body></html>";
        exit;
    }
} else {
    echo "<html><head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          </head><body>
          <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Campos de senha não foram preenchidos!',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.back();
            });
          </script>
          </body></html>";
    exit;
}

// Criptografar a senha
$senha_armazenada = password_hash($senha, PASSWORD_DEFAULT);

// Verificar se o email já está cadastrado
$email_verificado = $conn->query("SELECT * FROM loginsite WHERE email='$email'");
if ($email_verificado && $email_verificado->num_rows > 0) {
    echo "<html><head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      </head><body>
      <script>
        Swal.fire({
            icon: 'warning',
            title: 'E-mail já cadastrado!',
            text: 'Por favor, use outro e-mail.',
            confirmButtonText: 'OK',
            width: '400px',
            padding: '1.5rem',
            customClass: {
                popup: 'swal-wide'
            }
        }).then(() => {
            // Apenas retorna ao formulário, sem redirecionar
            window.history.back();
        });
      </script>
      <style>
          .swal-wide { width: 400px !important; }
      </style>
      </body></html>";
    exit;
}


// Gerar um token de ativação único
$token = bin2hex(random_bytes(16));

// Preparar a consulta SQL para inserir o usuário no banco com status "pendente"
$stmt = $conn->prepare("INSERT INTO loginsite (nome, email, telefone, senha, status, token) VALUES (?, ?, ?, ?, 'pendente', ?)");
if (!$stmt) {
    error_log("Erro ao preparar a consulta: " . $conn->error);
    die("Erro ao preparar a consulta SQL.");
}

$stmt->bind_param("sssss", $nome, $email, $telefone, $senha_armazenada, $token);

if (!$stmt->execute()) {
    error_log("Erro ao executar a consulta SQL: " . $stmt->error);
    echo "<html><head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          </head><body>
          <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao cadastrar no banco de dados: " . addslashes($stmt->error) . "',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.back();
            });
          </script>
          </body></html>";
    exit;
}

// Link de ativação com o token
$activationLink = "https://dmbarber.dmprojetos.com/login/config/ativar_conta.php?token=$token";

// Configurar e enviar o e-mail com PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        error_log("PHPMailer [{$level}]: {$str}");
    };

    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'dmprojetos120@gmail.com';
    $mail->Password = 'Deividlps120@';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('dmprojetos120@gmail.com', 'DM Projetos');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Bem-vindo(a) à DMPROJETOS! Confirmacao de Primeiro Acesso";
    $mail->Body = "
        <p>Olá $nome,</p>
        <p>É um prazer tê-lo(a) conosco na DMPROJETOS! Para ativar sua conta e acessar todos os nossos serviços, clique no link abaixo:</p>
        <p><a href='$activationLink'>Ativar Minha Conta</a></p>
        <p>Se precisar de qualquer ajuda ou tiver dúvidas, estamos à disposição. Entre em contato conosco pelo WhatsApp: <strong>(54) 99215-9272</strong>.</p>
        <p>Obrigado por escolher a DMPROJETOS!</p>
        <p>Atenciosamente,<br>DMPROJETOS</p>
    ";

    $mail->send();
    echo "<html><head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      </head><body>
      <script>
        Swal.fire({
            icon: 'success',
            title: 'Verifique seu e-mail para ativar sua conta.',
            text: 'Você tem 5 minutos para ativar via e-mail!',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = '../index.html';
        });
      </script></body></html>";

} catch (Exception $e) {
    error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
    echo "<html><head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          </head><body>
          <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao enviar o e-mail: " . addslashes($mail->ErrorInfo) . "',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.back();
            });
          </script>
          </body></html>";
    exit;
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>
