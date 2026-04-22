<?php
    require_once __DIR__ . '/config.php';


    
    $discord_url = "https://discord.com/oauth2/authorize?client_id=1101626565554618519&response_type=code&redirect_uri=https%3A%2F%2Fstanco.pl%2Fprocess-oauth.php&scope=identify+guilds+guilds.join+gdm.join";
    header("Location: $discord_url");
    exit();
?>