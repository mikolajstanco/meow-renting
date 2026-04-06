<?php
ini_set('display_errors', 1);
require_once "connection.php";
require_once 'config.php';

$connection = @new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno!=0) {
    echo "Error Database Connection";
}
else {
    $discordID = $_SESSION['userData']['discord_id'];
    $sql = "SELECT * FROM users WHERE discordID = '$discordID'";
    
    $discordIDcheck = $connection->query($sql);
    $discordName = $_SESSION['userData']['name'];
    if($discordIDcheck->num_rows > 0) {
        header("location: dashboard.php");
    } 
    elseif($connection->query("INSERT INTO `users`(`discordID`, `rentTime`, `discordUsername`) VALUES ('$discordID','0000-00-00 00:00:00','$discordName')"));
    
    $connection-> close();
}
header("location: dashboard.php");
exit();