<?php
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/connection.php';


    if(!isset($_SESSION['logged_in']) || $_SESSION['userData']['discord_id'] != $_ENV['DISCORD_ADMIN_ID']) {
        header("Location: index.php");
        exit();
    }
    

    $connection = new mysqli($host, $db_user, $db_password, $db_name);

    if ($connection->connect_errno != 0) {
        die("Błąd połączenia z bazą danych."); 
    } 

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_time') {
        $dID = $_POST['discordID'];
        $newTime = date("Y-m-d H:i:s", strtotime($_POST['termin']));
        
        addMoreTime($connection, $dID, $newTime);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    function addMoreTime($conn, $discordID, $timeToAdd) {
        $addMoreTimeQuery = $conn->prepare("UPDATE users SET rentTime = ? WHERE discordID = ?");
        $addMoreTimeQuery->bind_param("ss", $timeToAdd, $discordID);
        $addMoreTimeQuery->execute();
        $addMoreTimeQuery->close();
    }


    $stmt = $connection->prepare("SELECT * FROM users");
    $stmt->execute();
    $result = $stmt->get_result();


    $getWorkerLogs = $connection->prepare("SELECT * FROM Worker_Logs ORDER BY id DESC LIMIT 50");
    $getWorkerLogs->execute();
    $WorkerLogsResults = $getWorkerLogs->get_result();


?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #1e1f22;
        }
        th {
            background-color: #bdc3d6;
            color: #ffffff;
        }
        tr:hover {
            background-color: #85a2db;
        }
    </style>
</head>
<body>

    <h2>All Users</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Discord ID</th>
                <th>Rent time</th>
                <th>DC username</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $actualRenters = 0;

            while ($row = $result->fetch_assoc()) { 
                if ($row["rentTime"] != "2000-01-01 00:00:00") {
                    $actualRenters++; 
                }
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['discordID'] ?? 'NULL'); ?></td>
                    <td><?php echo htmlspecialchars($row['rentTime']); ?></td>
                    <td><?php echo htmlspecialchars($row['discordUsername'] ?? 'NULL'); ?></td>
                </tr>
            <?php 
            } 
            ?>
        </tbody>
    </table>
    <?php
        echo "Actual renters: $actualRenters";
    ?>
    <div class="form-box">
        <h3>Update rent time</h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_time">
            
            <label for="discordID">Discord ID:</label>
            <input type="text" id="discordID" name="discordID" placeholder="Discord ID" required>
            
            <label for="termin">New Date:</label>
            <input type="datetime-local" id="termin" name="termin" step="1" required>
            
            <button type="submit">Save</button>
        </form>
    </div>
    <div ckass="log-box">
        <h3>Log's from worker</h3>
<table>
        <thead>
            <tr>
                <th>ID</th>
                <th>DESCRIPTION</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
        <?php 

            while ($row = $WorkerLogsResults->fetch_assoc()) { 

                ?>
                
                <tr>
                    <td><?php echo htmlspecialchars($row['Id']); ?></td>
                    <td><?php echo htmlspecialchars($row['Description']); ?></td>
                    <td><?php echo htmlspecialchars($row['Status']); ?></td>
                </tr>
            <?php 
            } 
        ?>
    </tbody>
     </table>
    </div>
<?php

    $stmt->close();
    $getWorkerLogs->close();
    $connection->close();
?>

</body>
</html>