<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('/caminho/para/cliente_secret.json');
$client->setRedirectUri('http://dmestetica.dmprojetos.com/config/callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Obter informações do usuário
    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    // Armazenar informações do usuário na sessão
    $_SESSION['user_email'] = $userInfo->email;
    $_SESSION['user_name'] = $userInfo->name;

    // Redireciona o usuário para a página principal
    header('Location: /index.php');
    exit();
}
?>
