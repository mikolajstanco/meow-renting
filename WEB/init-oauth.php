<?php
    require_once __DIR__ . '/config.php';


    
    $discord_url = $_ENV['DISCORD_OAUTH_LINK'];
    header("Location: $discord_url");
    exit();
?>