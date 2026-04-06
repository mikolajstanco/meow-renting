<?php
require_once 'config.php';
require_once 'stripe-php-master/init.php';
// require_once 'secrets.php';
require_once 'connection.php';

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
$endpoint_secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

$payload    = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit();
}

if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;

    if ($session->payment_status !== 'paid') {
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
            $dt->modify('+1 month');
            break;
        default:
            error_log("Nieznany plan w metadata: {$plan}");
    }
    $rentTimeRaw = $dt->format('Y-m-d H:i:s');

    // --- tu robimy połączenie i escapujemy dane na tym samym obiekcie ---
    $conn = new mysqli($host, $db_user, $db_password, $db_name);
    if ($conn->connect_errno) {
        error_log("DB connect error: " . $conn->connect_error);
        http_response_code(500);
        exit();
    }

    $rentTime  = $conn->real_escape_string($rentTimeRaw);
    $discordId = $conn->real_escape_string($discordId);

    $sql = "UPDATE users
            SET rentTime = '$rentTime'
            WHERE discordID = '$discordId'";

    if (! $conn->query($sql)) {
        error_log("Error updating rent time: " . $conn->error);
        http_response_code(500);
        exit();
    }

    $conn->close();
}

http_response_code(200);
