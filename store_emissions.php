<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the user's state_id from the database
$query = "SELECT state_id FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($state_id);
$stmt->fetch();
$stmt->close();

if (!$state_id) {
    http_response_code(400);
    echo json_encode(["error" => "Unable to fetch state ID for the user."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    // Initialize
    $vehicleEmission = 0;
    $energyEmission = 0;
    $foodEmission = 0;
    $errors = [];

    foreach ($data as $activity) {
        $cat = isset($activity['category']) ? $activity['category'] : null;
        $subcat = isset($activity['subcategory']) ? $activity['subcategory'] : null;
        $amount = isset($activity['amount']) ? floatval($activity['amount']) : 0;

        if ($cat === 'transport') {
            $vehicleEmission += $amount;
        } elseif ($cat === 'energy') {
            $energyEmission += $amount;
        } elseif ($cat === 'food') {
            // If amount is per day, you can log as daily, or if per year, divide by 365
            $foodEmission += $amount; // adjust if needed
        }
    }

    $currentDate = date('Y-m-d');

    try {
        // Insert Transportation Emission
        $stmt = $conn->prepare("INSERT INTO Emissions (user_id, state_id, activity_id, emission_value, date) 
                                VALUES (?, ?, (SELECT activity_id FROM EmissionActivity WHERE activity_name = 'Transportation'), ?, ?)");
        $stmt->bind_param("iids", $user_id, $state_id, $vehicleEmission, $currentDate);
        $stmt->execute();

        // Insert Home Energy Emission
        $stmt = $conn->prepare("INSERT INTO Emissions (user_id, state_id, activity_id, emission_value, date) 
                                VALUES (?, ?, (SELECT activity_id FROM EmissionActivity WHERE activity_name = 'Home Energy'), ?, ?)");
        $stmt->bind_param("iids", $user_id, $state_id, $energyEmission, $currentDate);
        $stmt->execute();

        // Insert Food Emission
        $stmt = $conn->prepare("INSERT INTO Emissions (user_id, state_id, activity_id, emission_value, date) 
                                VALUES (?, ?, (SELECT activity_id FROM EmissionActivity WHERE activity_name = 'Food Consumption'), ?, ?)");
        $stmt->bind_param("iids", $user_id, $state_id, $foodEmission, $currentDate);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Emissions successfully recorded."]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    } finally {
        if ($stmt) $stmt->close();
        $conn->close();
    }
}
?>