<?php

#basic validation
if ( ! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

#standard hash
$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

#connect to db
$mysqli = require __DIR__ . "/db.php";

#signup statement
$sql = "INSERT INTO login (id_plantel, email, password_hash)
        VALUES (?, ?, ?)";

$stmt = $mysqli->stmt_init();

if ( ! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$id_plantel = 0;
$stmt->bind_param("sss",
                $id_plantel,
                $_POST["email"],
                $password_hash);


try {
    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    }
} catch (mysqli_sql_exception $e) {
    if ($mysqli->errno === 1062) {
        die("Email already taken");
    } else {
        die("Error: " . $mysqli->error . " " . $mysqli->errno);
    }
}
