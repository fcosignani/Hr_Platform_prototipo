<?php
require 'db.php';  // This includes your database connection

header('Content-Type: application/json');

try {
    // Decode the received JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    $id_clock = 0;
    if (!$data) {
        throw new Exception("No data provided or data is invalid.");
    }

    // Prepare SQL statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO clock (id_clock, name, id, date, dayOfWeek, exitTime, extraMinutesBefore21, extraMinutesAfter21) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind and execute the statement for each row of data
    foreach ($data as $row) {
        if (!isset($row['name'], $row['id'], $row['date'], $row['dayOfWeek'], $row['exitTime'], $row['extraMinutesBefore21'], $row['extraMinutesAfter21'])) {
            throw new Exception("One or more fields are missing in the data");
        }
        $stmt->bind_param("sssssss",$id_clock, $row['name'], $row['id'], $row['date'], $row['dayOfWeek'], $row['exitTime'], $row['extraMinutesBefore21'], $row['extraMinutesAfter21']);
        $stmt->execute();
    }

    // If everything is fine, send a success response
    echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully']);
} catch (Exception $e) {
    // Catch any exceptions and return an error message
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
