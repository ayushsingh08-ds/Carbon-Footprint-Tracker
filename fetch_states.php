<?php
header('Content-Type: application/json');
include 'db.php'; // Include your database connection file

// SQL query to fetch all states
$query = "SELECT state_id, state_name FROM states";
$result = $conn->query($query);

if (!$result) {
    echo json_encode(['error' => 'Failed to fetch states']);
    exit;
}

$states = [];
while ($row = $result->fetch_assoc()) {
    $states[] = $row;
}

// Return the states as JSON
echo json_encode($states);

$conn->close();
?>