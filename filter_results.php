<?php
include 'db.php'; // Include your database connection
$mysqli = require __DIR__ . "/db.php";

// Retrieve user input
$name = $_POST['name'] ?? '';
$startDate = $_POST['startDate'] ?? '';
$endDate = $_POST['endDate'] ?? '';

// Prepare and execute the query
$stmt = $mysqli->prepare("SELECT * FROM clock WHERE Name = ? AND Date BETWEEN ? AND ?");
$stmt->bind_param("sss", $name, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

// Start HTML output
echo '<!DOCTYPE html>';
echo '<html lang="es">';
echo '<head>';
echo '    <meta charset="UTF-8">';
echo '    <title>Filtered Results</title>';
echo '    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">';
echo '</head>';
echo '<body>';

// Check if the query returned any rows
if ($result->num_rows > 0) {
    echo "<table>";
    // Output the table header
    echo "<tr>";
    // Assuming you want to fetch the first row to get the column names
    $firstRow = $result->fetch_assoc();
    foreach ($firstRow as $column => $value) {
        echo "<th>" . htmlspecialchars($column) . "</th>";
    }
    echo "<th>Justification</th>"; // Additional header for the justification input
    echo "</tr>";
    
    // Output the first row data
    echo "<tr>";
    foreach ($firstRow as $value) {
        echo "<td>" . htmlspecialchars($value) . "</td>";
    }
    // Justification input for the first row
    echo "<td><input type='text' name='justification[" . htmlspecialchars($firstRow['ID']) . "]' placeholder='Enter justification'></td>";
    echo "</tr>";

    // Output the rest of the rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        // Justification input for each row
        echo "<td><input type='text' name='justification[" . htmlspecialchars($row['ID']) . "]' placeholder='Enter justification'></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No records found.";
}

echo '<button onclick="window.location.href=\'index.php\';">Return to Home</button>';
echo '</body>';
echo '</html>';

$mysqli->close();
?>
