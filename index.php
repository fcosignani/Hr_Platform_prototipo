<?php

session_start();

if (isset($_SESSION["user_id"])) {
    
    $mysqli = require __DIR__ . "/db.php";
    
    $sql = "SELECT * FROM login
            WHERE id_login = {$_SESSION["user_id"]}";
            
    $result = $mysqli->query($sql);
    
    $user = $result->fetch_assoc();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    
    <h1>Home</h1>
    
    <?php if (isset($user)): ?>
        
        <p>Hello <?= htmlspecialchars($user["email"]) ?></p>
        
        <p><a href="login.php">Log out</a></p>
        
    <?php else: ?>
        
        <p><a href="login.php">Log in</a> or <a href="signup.html">sign up</a></p>
        
    <?php endif; ?>

    <div style="text-align: center;">
        <h1>Quick Access Menu</h1>
        <nav>
            <ul style="list-style: none; padding: 0;">
                <li><a href="clock_upload.html" style="display: inline-block; padding: 10px; padding-left: 50px; padding-right: 50px; margin: 5px; background: #f8f8f8; border-radius: 10px;">Clock Upload</a></li>
                <li><a href="hour_just.html" style="display: inline-block; padding: 10px; padding-left: 50px; padding-right: 50px; margin: 5px; background: #f8f8f8; border-radius: 10px;">Hour Justification</a></li>
                <li><a href="work_order.html" style="display: inline-block; padding: 10px; padding-left: 50px; padding-right: 50px; margin: 5px; background: #f8f8f8; border-radius: 10px;">Work Order</a></li>
                <li><a href="request_hours.html" style="display: inline-block; padding: 10px; padding-left: 50px; padding-right: 50px; margin: 5px; background: #f8f8f8; border-radius: 10px;">Issue Payment</a></li>
            </ul>
        </nav>
    </div>
    
</body>
</html>
    
    
    
    
    
    
    
    
    
    
    