<?php
/ Iniciar a sessão
ession_start();
// Conectar ao banco de dados
host = '127.0.0.1:3306'; // Altere conforme sua configuração
db = 'u870367221_teste'; // Nome do seu banco de dados
user = 'u870367221_teste'; // Usuário do banco de dados
pass = 'Deividlps120@'; // Senha do banco de dados
$conn = new mysqli($host, $user, $pass, $db);
f ($conn->connect_error) {
   die("Falha na conexão: " . $conn->connect_error);

// Verificar se o usuário está logado
f (!isset($_SESSION['telefone'])) {
   echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
   exit();

// Obter o ID do agendamento a ser cancelado
id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Verificar se o ID é válido
f ($id > 0) {
   // Preparar a consulta para excluir o agendamento
   $stmt = $conn->prepare("DELETE FROM agendamentosbaiano WHERE id = ? AND telefone = ?");
   $stmt->bind_param("is", $id, $_SESSION['telefone']);
   
   if ($stmt->execute()) {
       if ($stmt->affected_rows > 0) {
           echo json_encode(['success' => true, 'message' => 'Agendamento cancelado com sucesso.']);
       } else {
           echo json_encode(['success' => false, 'message' => 'Nenhum agendamento encontrado para cancelar.']);
       }
   } else {
       echo json_encode(['success' => false, 'message' => 'Erro ao cancelar o agendamento.']);
   }
    $stmt->close();
 else {
   echo json_encode(['success' => false, 'message' => 'ID inválido.']);

$conn->close();
>