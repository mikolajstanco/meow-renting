<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/connection.php';

if (!isset($_SESSION['logged_in']) || empty($_SESSION['userData']['discord_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan = $_POST['plan'] ?? '';
    switch ($plan) {
        case 'plan1':
            $priceId = 'price_1RLPNo4h30UJpSUPBk0Jlens';
            break;
        case 'plan2':
            $priceId = 'price_1RLPOJ4h30UJpSUPvIy1XJFO';
            break;
        case 'plan3':
            $priceId = 'price_1RLQCW4h30UJpSUPKeGCuS6N';
            break;
        default:
            die('Nieprawidłowy plan.');
    }
} else {
    die('Brak danych POST.');
}

require_once __DIR__ . '/../stripe-php-master/init.php';
// require_once 'secrets.php';

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

$YOUR_DOMAIN = 'http://localhost:8888';

$checkoutSession = \Stripe\Checkout\Session::create([
    'payment_method_types'  => ['card'],
    'line_items'            => [[ 'price' => $priceId, 'quantity' => 1 ]],
    'mode'                  => 'payment',
    'allow_promotion_codes' => true,
    'metadata'              => [
        'discord_id' => $_SESSION['userData']['discord_id'],
        'plan'       => $plan,
    ],
    'success_url'           => $YOUR_DOMAIN . '/success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url'            => $YOUR_DOMAIN . '/index.php',
    'automatic_tax'         => ['enabled' => true],
]);

header('HTTP/1.1 303 See Other');
header('Location: ' . $checkoutSession->url);
exit();
