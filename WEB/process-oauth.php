<?php
require_once __DIR__ . '/config.php';
// session_start();

// if(!isset($_GET['code'])) {
//     header("Location: index.php");
//     exit();
// }

function addUserToGuild($discord_ID, $token, $guild_ID){
    $payload = [
        'access_token' => $token,
    ];
    
    // Zaktualizowany adres API do v10
    $discord_api_url = 'https://discord.com/api/v10/guilds/' . $guild_ID . '/members/' . $discord_ID;
    
    $bot_token = $_ENV['DISCORD_BOT_TOKEN'];
    $header = array("Authorization: Bot $bot_token", "Content-Type: application/json");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_URL, $discord_api_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
    $result = curl_exec($ch);
    
    if(!$result){
        echo curl_error($ch);
    } else {
        return true;
    }
}

$discord_code = $_GET['code'];

$payload = [
    'code' => $discord_code,
    'client_id' => '1101626565554618519',
    'client_secret' => $_ENV['DISCORD_CLIENT_SECRET'],
    'grant_type' => 'authorization_code',
    'redirect_uri' => 'https://stanco.pl/process-oauth.php',
    'scope' => 'identify guilds guilds.join',
];

$payload_string = http_build_query($payload);

$discord_token_url = "https://discord.com/api/v10/oauth2/token";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $discord_token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$token_response = curl_exec($ch);

if(!$token_response) {
    die("CURL Error: " . curl_error($ch));
}

$token_data = json_decode($token_response, true);

if (isset($token_data['error'])) {
    die("Błąd autoryzacji (Token): " . $token_data['error_description']);
}

$access_token = $token_data['access_token'];

$discord_users_url = "https://discord.com/api/v10/users/@me";

$header = array(
    "Authorization: Bearer $access_token", 
    "Content-Type: application/x-www-form-urlencoded"
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_URL, $discord_users_url);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$user_response = curl_exec($ch);
$user_data = json_decode($user_response, true);

if (!isset($user_data['id'])) {
    die("Błąd pobierania danych użytkownika: " . print_r($user_data, true));
}

// Zapis do sesji
$_SESSION['logged_in'] = true;
$_SESSION['userData'] = [
    'name' => $user_data['username'],
    'discord_id' => $user_data['id'],
    'avatar' => $user_data['avatar'],
    'access_token' => $access_token,
];

addUserToGuild($_SESSION['userData']['discord_id'], $_SESSION['userData']['access_token'], '1107813083554005072');

header("location: addtodb.php");
exit();
?>