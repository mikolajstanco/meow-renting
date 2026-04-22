<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/connection.php';

// Inicjalizacja kluczy Stripe (biblioteka jest już załadowana przez Composera w config.php)
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
$endpoint_secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

$payload    = @file_get_contents('php://input');
// Zabezpieczenie przed brakiem nagłówka (gdybyśmy weszli na stronę bezpośrednio z przeglądarki)
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

// --- SYSTEM LOGOWANIA BŁĘDÓW (Szpieg) ---
$log_msg = "\n[" . date('Y-m-d H:i:s') . "] Webhook start. Secret: " . substr($endpoint_secret, 0, 7) . "... | Sygnatura: " . substr($sig_header, 0, 7) . "...\n";
file_put_contents(__DIR__ . '/webhook_log.txt', $log_msg, FILE_APPEND);

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    file_put_contents(__DIR__ . '/webhook_log.txt', "-> Sukces: Sygnatura ZAAKCEPTOWANA!\n", FILE_APPEND);
} catch (\UnexpectedValueException $e) {
    file_put_contents(__DIR__ . '/webhook_log.txt', "-> Błąd 400: Niepoprawny format danych (Payload).\n", FILE_APPEND);
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    file_put_contents(__DIR__ . '/webhook_log.txt', "-> Błąd 400: Sygnatura odrzucona! (Zły klucz w .env LUB problem z proxy Cloudflare).\n", FILE_APPEND);
    http_response_code(400);
    exit();
}

// Jeśli event to udana płatność
if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;

    if ($session->payment_status !== 'paid' && $session->payment_status !== 'no_payment_required') {
        file_put_contents(__DIR__ . '/webhook_log.txt', "-> Odrzucono: Status płatności to " . $session->payment_status . "\n", FILE_APPEND);
        http_response_code(400);
        exit();
    }
    
    $discordId = $session->metadata->discord_id;
    $plan      = $session->metadata->plan;

    date_default_timezone_set('Europe/Warsaw');
    $dt = new DateTime();
    
    switch ($plan) {
        case 'plan1':
            $dt->modify('+1 day');
            break;
        case 'plan2':
            $dt->modify('+7 days');
            break;
        case 'plan3':
            $dt->modify('+1 hour');
            break;
        default:
            file_put_contents(__DIR__ . '/webhook_log.txt', "-> Błąd 400: Nieznany plan: {$plan}\n", FILE_APPEND);
            http_response_code(400);
            exit();
    }
    
    $rentTimeRaw = $dt->format('Y-m-d H:i:s');

    $conn = new mysqli($host, $db_user, $db_password, $db_name);
    if ($conn->connect_errno) {
        file_put_contents(__DIR__ . '/webhook_log.txt', "-> Błąd 500: Problem z bazą danych: " . $conn->connect_error . "\n", FILE_APPEND);
        http_response_code(500);
        exit();
    }

    $stmt = $conn->prepare("UPDATE users SET rentTime = ? WHERE discordID = ?");
    $stmt->bind_param("ss", $rentTimeRaw, $discordId);
    
    if (!$stmt->execute()) {
        file_put_contents(__DIR__ . '/webhook_log.txt', "-> Błąd 500: Nie udało się zaktualizować bazy: " . $stmt->error . "\n", FILE_APPEND);
        http_response_code(500);
        exit();
    }
    
    $stmt->close();
    $conn->close();
    
    file_put_contents(__DIR__ . '/webhook_log.txt', "-> Sukces: Baza zaktualizowana dla usera: {$discordId}, nowy czas: {$rentTimeRaw}\n", FILE_APPEND);
}

http_response_code(200);
?>