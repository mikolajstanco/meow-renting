<?php
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/connection.php';

    if(!isset($_SESSION['logged_in'])) {
        header("Location: init-oauth.php");
        exit();
    }

    $name = $_SESSION['userData']['name'];
    
    $connection = new mysqli($host, $db_user, $db_password, $db_name);
    
    if ($connection->connect_errno != 0) {
        die("Błąd połączenia z bazą danych."); 
    } else {
        $discordID = $_SESSION['userData']['discord_id'];
        
        $stmt = $connection->prepare("SELECT rentTime FROM users WHERE discordID = ?");
        $stmt->bind_param("s", $discordID);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row != NULL) {
            $_SESSION['rentTime'] = $row['rentTime'];
        } else {
            $_SESSION['rentTime'] = '0000-00-00 00:00:00'; 
        }
        $stmt->close();
        $connection->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>   
    <title>Dashboard</title>
    <link rel="icon" href="src/favicon.png" type="image/png">

    <meta charset="UTF-8">
        <title>Meow Industry</title>

        <link rel="stylesheet" href="style.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
        <div class="header">
            <a href="index.php" class="logo">Meow Industry</a> 

            <div class="header-right-side">
                <a href="index.php#contact" class="right-header">CONTACT</a>
                <a href="index.php#faq" class="right-header">FAQ</a>
                <a href="index.php#pricing" class="right-header">PRICING</a>
                <button class="dashboard-button" onclick="location.href='dashboard.php'" type="button">Dashboard</button>
            </div>
        </div>
        <div class="main">
                    <div class="dashboard-content">
                        <div id="loggedAS">
                            <p class="noen">Welcome, </p>
                            &nbsp
                            <div class="discordUserName"> <?php echo $name  ?></div>
                        </div>
                        <div class="dashboard-header">
                            <?php
                                if ($_SESSION['rentTime'] != '2000-01-01 00:00:00') {
                                    echo "<p class='actual-rent'>ACTUAL RENT END TIME: ".$_SESSION['rentTime']." </p>";
                                }
                                else {
                                     echo "<p class='actual-rent'>ACTUALLY NOT RENTED";
                                }
                            ?>
                        
                            <br>
                            <br>
                            
                        </div>
                        <div class="dashboard-footer">
                            <a href="https://discord.gg/gUC8u2FWXF" target="_blank" rel="noopener noreferrer">
                                <img class="discord-invite" src="src/discord-invite.png" alt="Join Discord">
                            </a>
                            &nbsp
                            &nbsp
                            &nbsp

                            <a href="logOut.php" class="log-out-butt">Log Out</a>
                        <div>
                    </div>              
        </div>
        <div class="footer"></div>
    </body>
</html>