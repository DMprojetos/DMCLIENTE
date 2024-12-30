<?php
// Inclua manualmente os arquivos do PHPMailer
require '/home/u870367221/domains/dmbarber.dmprojetos.com/public_html/login/config/src/Exception.php';
require '/home/u870367221/domains/dmbarber.dmprojetos.com/public_html/login/config/src/PHPMailer.php';
require '/home/u870367221/domains/dmbarber.dmprojetos.com/public_html/login/config/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Habilitar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt'); // Arquivo de log de erros

// Conexão com o banco de dados
$servername = "127.0.0.1:3306";
$username = "u870367221_dmpainel";
$password = "Deividlps120@";
$dbname = "u870367221_dmpainel";

// Criar a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão com o banco de dados
if ($conn->connect_error) {
    error_log("Erro na conexão com o banco de dados: " . $conn->connect_error);
    die("Conexão falhou: " . $conn->connect_error);
}

// Receber e sanitizar o e-mail do formulário
$email = $conn->real_escape_string($_POST['email'] ?? '');

// Verificar se o e-mail está cadastrado
$email_verificado = $conn->query("SELECT * FROM loginsite WHERE email='$email'");
if ($email_verificado && $email_verificado->num_rows > 0) {
    // Gera um token de recuperação e define uma data de expiração para o link
    $token = bin2hex(random_bytes(16));
    $expiracao = date("Y-m-d H:i:s", strtotime('+1 hour'));

    // Inserir o token no banco de dados para recuperação de senha
$query = $conn->prepare("INSERT INTO recuperacao_senha (email, token, expiracao) VALUES (?, ?, ?)");
if (!$query) {
    error_log("Erro ao preparar a consulta: " . $conn->error);
    echo "<html><head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      </head><body>
      <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Erro ao preparar a consulta SQL.',
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

    $query->bind_param("sss", $email, $token, $expiracao);
if (!$query->execute()) {
    error_log("Erro ao executar a consulta SQL: " . $query->error);
    echo "<html><head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      </head><body>
      <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Erro ao salvar o token no banco de dados.',
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


    // Link de redefinição de senha
    $resetLink = "https://dmbarber.dmprojetos.com/login/RecuperarSenha.php?token=$token";

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
        $mail->Subject = "Redefinicao de Senha - DM Projetos";
        $mail->Body = "
            <p>Olá,</p>
            <p>Recebemos uma solicitação para redefinir a senha da sua conta. Para redefinir sua senha, clique no link abaixo:</p>
            <p><a href='$resetLink'>Redefinir Minha Senha</a></p>
            <p>Este link é válido por 1 hora.</p>
            <p>Se você não solicitou a redefinição de senha, ignore este e-mail.</p>
            <p>Atenciosamente,<br>DM Projetos</p>
        ";

        $mail->send();
echo "<html><head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
  </head><body>
  <script>
    Swal.fire({
        icon: 'success',
        title: 'E-mail enviado!',
        text: 'Um e-mail de recuperação foi enviado. Verifique sua caixa de entrada.',
        confirmButtonText: 'OK',
        width: '400px',
        padding: '2rem',
        customClass: {
            popup: 'swal-wide'
        }
    }).then(() => {
        window.location.href = '../index.html';
    });
  </script>
  <style>
      .swal-wide { width: 400px !important; }
  </style>
  </body></html>";

    } catch (Exception $e) {
    error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
    echo "<html><head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      </head><body>
      <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro ao enviar e-mail',
            text: '" . addslashes($mail->ErrorInfo) . "',
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


} else {
    echo "<html><head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
      </head><body>
      <script>
        Swal.fire({
            icon: 'warning',
            title: 'E-mail não encontrado',
            text: 'E-mail não encontrado no sistema.',
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
}


// Fechar a conexão
$conn->close();
?>
