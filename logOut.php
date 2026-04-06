<?php
    require_once 'config.php';

        unset($_SESSION['logged_in']);
        unset($_SESSION['userData']);

        header("Location: index.php");
        
    exit();
?>

