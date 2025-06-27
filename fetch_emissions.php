<?php
// Include database connection
include 'db.php';

// Start the session to retrieve the logged-in user's ID
session_start();

// Get the user ID from the session
// Ensure the user is logged in and the user_id exists in the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id']; // Retrieve the logged-in user's ID

try {
    // SQL query to calculate total emissions for the logged-in user grouped by date
    $sql = "
        SELECT 
            date, 
            user_id,
            SUM(CASE WHEN activity_id = 1 THEN emission_value ELSE 0 END) AS travel_emissions,
            SUM(CASE WHEN activity_id = 2 THEN emission_value ELSE 0 END) AS energy_emissions,
            SUM(CASE WHEN activity_id = 3 THEN emission_value ELSE 0 END) AS waste_emissions,
            SUM(emission_value) AS total_emissions
        FROM emissions
        WHERE user_id = ? -- Filter by logged-in user's ID
        GROUP BY user_id, date
        ORDER BY date DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id); // Bind the logged-in user's ID
    $stmt->execute();
    $result = $stmt->get_result();

    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }

    // Output the records as JSON
    echo json_encode($records);
} catch (Exception $e) {
    // Handle any errors
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>