<?php
    require_once 'config.php';
    // session_start();

    //     if(isset($_SESSION['logged_in'])) {
    //     header("Location: dashboard.php");
    //     exit();
    // }


    
    $discord_url = "https://discord.com/oauth2/authorize?client_id=1101626565554618519&response_type=code&redirect_uri=http%3A%2F%2Flocalhost%3A8888%2Fprocess-oauth.php&scope=identify+guilds+guilds.join+gdm.join";
    header("Location: $discord_url");
    exit();
?>