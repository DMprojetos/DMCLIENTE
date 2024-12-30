<?php
// Iniciar a sessão
session_start();

// Incluir o arquivo para excluir contas pendentes
include 'delete_pendente.php';

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
    error_log("Erro na conexão com o banco de dados: " . $conn->connect_error);
    echo "<html><head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          </head><body>
          <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Falha na conexão com o banco de dados: " . addslashes($conn->connect_error) . "',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.back();
            });
          </script>
          </body></html>";
    exit;
}

// Verificar se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber e sanitizar os dados do formulário
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $_POST['senha'];

    // Verificar se o usuário existe e está ativo
    $sql = "SELECT * FROM loginsite WHERE email='$email' AND status='ativo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // O email foi encontrado e a conta está ativa
        $user = $result->fetch_assoc();

        // Verificar a senha usando password_verify()
        if (password_verify($senha, $user['senha'])) {
            // Senha correta, login bem-sucedido
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['telefone'] = $user['telefone'];
            $_SESSION['loggedin'] = true;

            // Redirecionar para a URL armazenada ou para a página inicial
            $redirect_url = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'https://dmbarber.dmprojetos.com/site/';
            unset($_SESSION['redirect_after_login']); // Limpar a variável após o uso
            header("Location: $redirect_url");
            exit();
        } else {
            // Senha incorreta
            echo "<html><head>
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  </head><body>
                  <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Senha incorreta!',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.history.back();
                    });
                  </script>
                  </body></html>";
            exit;
        }
    } else {
        // Conta inativa ou email não encontrado
        echo "<html><head>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              </head><body>
              <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Conta não encontrada ou ainda não ativada. Verifique seu e-mail para ativação.',
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
                text: 'Método de requisição inválido.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.back();
            });
          </script>
          </body></html>";
    exit;
}

// Fechar a conexão
$conn->close();
?>