<?php
require_once 'config.php';
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $traffic = $_POST["plan"];
    }
    if ($traffic === "plan1") {
        header("Location: https://buy.stripe.com/9AQ2bH36P9cq7Cw6oo");
        
        exit();
    } elseif ($traffic === "plan2") {
        header("Location: https://buy.stripe.com/7sI4jPbDl0FU3mg6op");
        exit();
    } elseif ($traffic === "plan3") {
        header("Location: https://buy.stripe.com/9AQg2x0YHewKg929AC");
        exit();
    }
?>