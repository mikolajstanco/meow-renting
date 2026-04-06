<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/connection.php';

ini_set('display_errors', 1);

$connection = new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno != 0) {
    die("Błąd połączenia z bazą danych.");
} else {
    $discordID = $_SESSION['userData']['discord_id'];
    $discordName = $_SESSION['userData']['name'];

    $stmt = $connection->prepare("SELECT id FROM users WHERE discordID = ?");
    $stmt->bind_param("s", $discordID);
    $stmt->execute();
    
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $connection->close();
        header("location: dashboard.php");
        exit();
    } else {
        $stmt->close();

        $insert_stmt = $connection->prepare("INSERT INTO users (discordID, rentTime, discordUsername) VALUES (?, '0000-00-00 00:00:00', ?)");
        $insert_stmt->bind_param("ss", $discordID, $discordName);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    $connection->close();
}

header("location: dashboard.php");
exit();
?>